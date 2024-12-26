<?php

namespace EomPlus\Version\Console\Commands;

use Illuminate\Console\Command;

class VersionInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version:init';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentPath = getcwd();
        $filePath = $currentPath. '/version.json';
        if (file_exists($filePath)) {
	   $this->error('File version.js already exists at '. $currentPath);
        } else {

                        $version = (object) [
                          'major' => 0,
                          'minor' => 0,
                          'patch' => 0,
                        ];
           file_put_contents($filePath, json_encode($version, JSON_PRETTY_PRINT));
           $this->info('File version.js has been created at '. $currentPath);
        }
    }

}
