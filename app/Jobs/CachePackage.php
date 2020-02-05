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

    /**
     * CachePackage constructor.
     * @param String $packageUrl
     * @return void
     */
    public function __construct(String $packageUrl)
    {
        $this->packageUrl = $packageUrl;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::where('email', 'system-cache@repo.local')->first();
        $filePath = '/tmp/' . Str::random(32) . '.nupkg';
        $fileStream = fopen($filePath, 'w+');
        $client = new Client([]);

        $dlRequest = new Request('GET', $this->packageUrl);
        $client->send($dlRequest, ['sink' => $fileStream, 'allow_redirects' => true]);

        Storage::makeDirectory('packages');
        $nupkg = new NupkgFile($filePath);
        $nupkg->savePackage($user);
        unlink($filePath);
    }
}
