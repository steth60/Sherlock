<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\LaraUpdater;

class CommandCurrentVersion extends Command
{
    protected $signature = 'update:current-version';
    protected $description = 'Get the current version';

    protected $updater;

    public function __construct(LaraUpdater $updater)
    {
        parent::__construct();
        $this->updater = $updater;
    }

    public function handle()
    {
        $version = $this->updater->getCurrentVersion();
        $this->info('Current version: ' . $version);
    }
}
