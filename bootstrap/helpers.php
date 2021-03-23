<?php

use App\Choco\NuGet\NugetPackage;
use App\Model\User;
use App\Nuget\NupkgFile;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function getStringBetween($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function createUserAccount($name, $email, $password = null)
{
    do {
        $key = Str::random(32);
    } while (User::where('apikey', $key)->count() > 0);
    $user = new User;
    $user->name = $name;
    $user->email = $email;
    $user->password = Hash::make(empty($password) ? Str::random(16) : $password);
    $user->apikey = $key;
    $user->save();
    return $user;
}

function generateChocoPackageUrl($id, $version)
{
    $packageSlug = '/api/v2/package/' . $id . '/' . $version;
    return 'https://chocolatey.org' . $packageSlug;
}

function generateChocoLatestPackageUrl($id)
{
    $packageSlug = '/api/v2/Packages()?$filter=(tolower(Id)%20eq%20\'' . $id . '\')%20and%20IsLatestVersion';
    return 'https://chocolatey.org' . $packageSlug;
}

function generateLicensedChocoPackageUrl($id, $version)
{
    $packageSlug = '/api/v2/package/' . $id . '/' . $version;
    $license_id = config('choco.license_id', false);
    if ($license_id) {
        return 'https://customer:' . $license_id . '@licensedpackages.chocolatey.org' . $packageSlug;
    }
    return false;
}

function getLatestVersion($id)
{
    $latestUrl = generateChocoLatestPackageUrl(strtolower($id));
    $client = new Client();
    $cache_key = strtolower('check-latest-' . $id);
    $inProgress = Cache::get($cache_key);
    if (empty($inProgress)) {
        Cache::put($cache_key, true, 60);
        try {
            if (in_array(strtolower($id), config('choco.ignore_updates_on', []))) throw new Exception('Local only package!');
            $res = $client->get($latestUrl);
            $xmlString = $res->getBody()->getContents();
            $xml = json_decode(json_encode(simplexml_load_string($xmlString)), true);
            $package_url = $xml['entry']['content']['@attributes']['src'];
            $urlParts = explode('/', parse_url($package_url, PHP_URL_PATH));
            $version = $urlParts[count($urlParts) - 1];
            $id = $urlParts[count($urlParts) - 2];
            if (empty($id) || empty($version)) return null;
            Cache::forget($cache_key);
            return ['id' => $id, 'version' => $version];
        } catch (Exception $e) {
            // If something went wrong return the latest version from the DB
            $package = NugetPackage::where('package_id', $id)
                ->where('is_latest_version', true)
                ->first();
            if (!empty($package)) return ['id' => $package->package_id, 'version' => $package->version];
            Cache::forget($cache_key);
            return null;
        }
    } else {
        $count = 0;
        while ($count <= 5) {
            $status = Cache::get($cache_key);
            if (!empty($status)) {
                usleep(100);
            } else {
                $package = NugetPackage::where('package_id', $id)
                    ->where('is_latest_version', true)
                    ->first();
                if (!empty($package)) return ['id' => $package->package_id, 'version' => $package->version];
                return null;
            }
            $count++;
        }
    }
}

function cachePackage($id, $version)
{
    if (!empty($id) && strtolower($version) === 'latest') {
        $latest = getLatestVersion($id);
        $version = empty($latest) ? null : $latest['version'];
        $package = NugetPackage::where('package_id', $id)
            ->where('version', $version)
            ->first();
        // If the package exists return it
        if (!empty($package)) return $package;
    }

    if (!empty($id) && !empty($version)) {
        if (!in_array($id, config('choco.ignore_updates_on', []), true)) {
            $cache_key = strtolower('caching-' . $id . '-' . $version);
            $inProgress = Cache::get($cache_key);
            if (empty($inProgress)) {
                Cache::put($cache_key, true, 60);
                $user = User::where('email', 'system-cache@repo.local')->first();
                if (empty($user)) $user = createUserAccount('system-cache', 'system-cache@repo.local');
                $packageUrl = generateChocoPackageUrl($id, $version);
                $filePath = '/tmp/' . Str::random(32) . '.nupkg';
                $client = new Client([]);
                $dlRequest = new Request('GET', $packageUrl);
                $fileStream = fopen($filePath, 'w+');
                $res = $client->send($dlRequest, ['sink' => $fileStream, 'allow_redirects' => true, 'http_errors' => false]);
                Storage::makeDirectory('packages');
                if ($res->getStatusCode() === 200) {
                    $nupkg = new NupkgFile($filePath);
                    $nupkg->savePackage($user);
                    unlink($filePath);
                    Cache::forget($cache_key);
                    return NugetPackage::where('package_id', $id)
                        ->where('version', $version)
                        ->first();
                } else {
                    // Try to get the file from the licensed choco repo
                    $licensedUrl = generateLicensedChocoPackageUrl($id, $version);
                    if ($licensedUrl) {
                        $client = new Client([]);
                        $dlRequest = new Request('GET', $licensedUrl);
                        $filePath = '/tmp/' . Str::random(32) . '.nupkg';
                        $fileStream = fopen($filePath, 'w+');
                        $res = $client->send($dlRequest, ['sink' => $fileStream, 'allow_redirects' => true, 'http_errors' => false]);
                        Storage::makeDirectory('packages');
                        if ($res->getStatusCode() === 200) {
                            $nupkg = new NupkgFile($filePath);
                            $nupkg->savePackage($user);
                            unlink($filePath);
                            Cache::forget($cache_key);
                            return NugetPackage::where('package_id', $id)
                                ->where('version', $version)
                                ->first();
                        }
                    }
                }
                return false;
            } else {
                $count = 0;
                while ($count <= 5) {
                    $status = Cache::get($cache_key);
                    if (!empty($status)) {
                        usleep(100);
                    } else {
                        return NugetPackage::where('package_id', $id)
                            ->where('version', $version)
                            ->first();
                    }
                    $count++;
                }
            }
        }
        return true;
    }
    return false;
}
