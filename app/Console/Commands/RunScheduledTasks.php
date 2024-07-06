<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use App\Models\Instance;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class RunScheduledTasks extends Command
{
    protected $signature = 'run:scheduled-tasks';
    protected $description = 'Run scheduled tasks';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();
        $currentMonth = $now->format('M');
        $currentDay = $now->format('D');
        $currentHour = $now->format('H');
        $currentMinute = $now->format('i');

        $schedules = Schedule::where('enabled', true)
            ->whereJsonContains('months', $currentMonth)
            ->whereJsonContains('days', $currentDay)
            ->whereJsonContains('hours', $currentHour)
            ->whereJsonContains('minutes', $currentMinute)
            ->get();

        foreach ($schedules as $schedule) {
            $instance = $schedule->instance;

            if ($schedule->action === 'start') {
                $this->startInstance($instance);
            } elseif ($schedule->action === 'stop') {
                $this->stopInstance($instance);
            } elseif ($schedule->action === 'restart') {
                $this->restartInstance($instance);
            }
        }
    }

    private function startInstance(Instance $instance)
    {
        // Logic to start the instance
        Log::info("Starting instance: {$instance->id}");
        // Execute the start command
    }

    private function stopInstance(Instance $instance)
    {
        // Logic to stop the instance
        Log::info("Stopping instance: {$instance->id}");
        // Execute the stop command
    }

    private function restartInstance(Instance $instance)
    {
        // Logic to restart the instance
        Log::info("Restarting instance: {$instance->id}");
        // Execute the restart command
    }
}
