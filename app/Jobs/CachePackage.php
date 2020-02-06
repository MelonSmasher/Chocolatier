<?php

namespace App\Jobs;

use App\Nuget\NupkgFile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Model\User;
Use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CachePackage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $packageUrl;

    protected $packageId;

    /**
     * CachePackage constructor.
     * @param String $packageUrl
     * @return void
     */
    public function __construct(String $packageUrl, String $packageId)
    {
        $this->packageUrl = $packageUrl;
        $this->packageId = $packageId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ignore = config('choco.ignore_updates_on', []);
        if (!in_array($this->packageId, $ignore, true)) {
            $user = User::where('email', 'system-cache@repo.local')->first();
            $filePath = '/tmp/' . Str::random(32) . '.nupkg';
            $fileStream = fopen($filePath, 'w+');
            $client = new Client([]);

            $dlRequest = new Request('GET', $this->packageUrl);
            $res = $client->send($dlRequest, ['sink' => $fileStream, 'allow_redirects' => true, 'http_errors' => false]);
            Storage::makeDirectory('packages');

            if ($res->getStatusCode() === 200) {
                $nupkg = new NupkgFile($filePath);
                $nupkg->savePackage($user);
                unlink($filePath);
            }
        }
    }
}
