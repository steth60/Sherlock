<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\LaraUpdater;

class CommandUpdate extends Command
{
    protected $signature = 'update:install';
    protected $description = 'Install the latest update';

    protected $updater;

    public function __construct(LaraUpdater $updater)
    {
        parent::__construct();
        $this->updater = $updater;
    }

    public function handle()
    {
        $this->updater->installUpdate();
        $this->info('Update installed successfully.');
    }
}
