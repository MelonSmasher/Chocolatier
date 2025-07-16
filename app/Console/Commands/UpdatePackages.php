<?php

namespace App\Console\Commands;

use App\Choco\NuGet\NugetPackage;
use App\Model\User;
use App\Nuget\NupkgFile;
use GuzzleHttp\Psr7\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
Use GuzzleHttp\Client;

class UpdatePackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates all local packages to the latest version';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Storage::makeDirectory('packages');
        $user = User::where('email', 'system-cache@repo.local')->first();
        $processed = [];
        $packages = NugetPackage::where('is_absolute_latest_version', true)->get();

        $ignore = config('choco.ignore_updates_on', []);

        $bar = $this->output->createProgressBar(count($packages));
        $bar->start();

        foreach ($packages as $pkg) {
            if (!in_array($pkg->package_id, $processed, true) && !in_array($pkg->package_id, $ignore, true)) {
                $packageUrl = 'https://community.chocolatey.org/api/v2/package/' . $pkg->package_id . '/';
                $tmpFilePath = '/tmp/' . Str::random(32) . '.nupkg';
                $tmpFileStream = fopen($tmpFilePath, 'w+');
                $client = new Client([]);
                $dlRequest = new Request('GET', $packageUrl);
                $res = $client->send($dlRequest, ['sink' => $tmpFileStream, 'allow_redirects' => true, 'http_errors' => false]);
                if ($res->getStatusCode() === 200) {
                    $tmpNupkg = new NupkgFile($tmpFilePath);
                    if ($tmpNupkg->getNuspec()->version != $pkg->version) {
                        $tmpNupkg->savePackage($user);
                        $this->info($pkg->package_id . ': updated');
                    }
                    unlink($tmpFilePath);
                } else {
                    $this->warn($pkg->package_id . ': not found on chocolatey.org');
                }
                $processed[] = $pkg->package_id;
                $bar->advance();
            }
        }

        $bar->finish();
        echo "\n";
    }
}
