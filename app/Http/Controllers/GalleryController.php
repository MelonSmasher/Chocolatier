<?php

namespace App\Http\Controllers;

use App\Choco\NuGet\NugetPackage;
use App\Jobs\CachePackage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function home()
    {
        return view('gallery.home')
            ->with('uniquePackages', NugetPackage::distinct('package_id')
                ->count('package_id'))
            ->with('totalDownloads', NugetPackage::sum('version_download_count'))
            ->with('totalPackages', NugetPackage::count());
    }

    /**
     * @return View
     */
    public function index()
    {
        $filter = request('by', 'most');
        $count = request('count', 30);
        if ($count > 100) $count = 100;

        switch ($filter) {
            case 'most':
                $packages = NugetPackage::where('is_absolute_latest_version', true)->orderBy('download_count', 'desc')->paginate($count);
                break;
            case 'least':
                $packages = NugetPackage::where('is_absolute_latest_version', true)->orderBy('download_count', 'asc')->paginate($count);
                break;
            case 'title':
                $packages = NugetPackage::where('is_absolute_latest_version', true)->orderBy('title')->paginate($count);
                break;
            case 'new':
                $packages = NugetPackage::where('is_absolute_latest_version', true)->orderBy('updated_at', 'desc')->paginate($count);
                break;
            case 'old':
                $packages = NugetPackage::where('is_absolute_latest_version', true)->orderBy('updated_at', 'asc')->paginate($count);
                break;
            default:
                $filter = 'most';
                $packages = NugetPackage::where('is_absolute_latest_version', true)->orderBy('download_count', 'desc')->paginate($count);
                break;
        }

        $data = [
            'packages' => $packages, // Keep previous get params but allow the paginator to take care of the page param
            'filter' => $filter // Pass this for contextual messages in the view
        ];

        return view('gallery.index', $data);
    }

    public function showPackage($name, $version = null)
    {
        if (!empty($version)) {
            $package = NugetPackage::where('package_id', $name)->where('version', $version)->first();
            if (empty($package)) {
                if (strtolower($version) !== 'latest') {
                    $package = cachePackage($name, $version);
                }
                if (empty($package)) return response('Could not find that package!', 404);
            }
        } else {
            $package = NugetPackage::where('package_id', $name)->where('is_absolute_latest_version', true)->first();
        }
        return view('gallery.show')
            ->with('package', $package)
            ->with('versions', $package->versions());
    }
}
