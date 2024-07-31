<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\LaraUpdater;

class CommandCheck extends Command
{
    protected $signature = 'update:check';
    protected $description = 'Check for updates';

    protected $updater;

    public function __construct(LaraUpdater $updater)
    {
        parent::__construct();
        $this->updater = $updater;
    }

    public function handle()
    {
        $updateAvailable = $this->updater->checkForUpdate();
        if ($updateAvailable) {
            $this->info('An update is available.');
        } else {
            $this->info('No updates available.');
        }
    }
}
