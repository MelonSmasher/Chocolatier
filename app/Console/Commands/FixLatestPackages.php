<?php

namespace App\Console\Commands;

use App\Choco\NuGet\NugetPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use PHLAK\SemVer;

class FixLatestPackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:latest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command fixes any duplicate latest packages';

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
        $packageIds = DB::table('nuget_packages')
            ->select('package_id')
            ->groupBy('package_id')
            ->get()
            ->all();

        $bar = $this->output->createProgressBar(count($packageIds));
        $bar->start();

        foreach ($packageIds as $id) {
            $packages = NugetPackage::where('is_absolute_latest_version', true)->where('package_id', $id->package_id)->get();
            try {
                if (count($packages) > 1) {
                    $highest = $packages[0];
                    $highestVer = new SemVer\Version($packages[0]->version);
                    foreach ($packages as $package) {
                        if ($package->id !== $highest->id) {
                            $cVer = new SemVer\Version($package->version);
                            if ($highestVer->lt($cVer)) {
                                $highest->is_absolute_latest_version = false;
                                $highest->save();
                                $highest = $package;
                                $highestVer = new SemVer\Version($package->version);
                                $this->info($id->package_id . ': found new highest version: ' . $highestVer . ' < ' . $cVer);
                            } else {
                                $package->is_absolute_latest_version = false;
                                $package->save();
                            }
                        }
                    }
                }
            } catch (SemVer\Exceptions\InvalidVersionException $e) {
                $this->warn($e->getMessage().' skipping...');
            }
            $bar->advance();
        }

        $bar->finish();
        echo "\n";
    }
}
