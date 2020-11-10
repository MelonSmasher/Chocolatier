<?php

use App\Choco\NuGet\NugetPackage;
use App\Model\User;
use App\Nuget\NupkgFile;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


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
    $license_id = config('choco.license_id', false);
    if ($license_id) {
        return 'https://customer:' . $license_id . '@licensedpackages.chocolatey.org' . $packageSlug;
    }
    return 'https://chocolatey.org' . $packageSlug;
}

function cachePackage($id, $version)
{
    if (!empty($id) && !empty($version) && strtolower($version) !== 'latest') {
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
