<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearCacheCommand extends Command
{
    protected $signature = 'system:clear-cache';
    protected $description = 'Clear caches';

    public function handle()
    {
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->call('cache:clear');
        $this->info('Caches cleared successfully.');
    }
}
