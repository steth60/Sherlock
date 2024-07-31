<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Instance\InstanceController;
use App\Models\Instance;

class CaptureMetrics extends Command
{
    protected $signature = 'metrics:capture';
    protected $description = 'Capture CPU and Memory usage for running instances';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $instances = Instance::where('status', 'running')->get();
        foreach ($instances as $instance) {
            (new InstanceController)->captureMetrics($instance);
        }

        return 0;
    }
}
