<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Instance;
use Illuminate\Support\Facades\Log;

class CheckInstanceStatus extends Command
{
    protected $signature = 'instance:check-status';
    protected $description = 'Check if running instances are still active and update their status if they are not';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $runningInstances = Instance::where('status', 'running')->get();

        foreach ($runningInstances as $instance) {
            if ($instance->pid) {
                $commandCheck = 'ps -p ' . $instance->pid;
                $checkProcess = shell_exec($commandCheck);

                if (strpos($checkProcess, (string) $instance->pid) === false) {
                    // Process is no longer running
                    $instance->status = 'stopped';
                    $instance->pid = null;
                    $instance->save();

                    Log::info('Instance status updated to stopped', ['instance_id' => $instance->id]);
                }
            }
        }

        return 0;
    }
}
