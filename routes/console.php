<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use App\Models\Schedule as TaskSchedule;
use App\Http\Controllers\InstanceController;
use Cron\CronExpression;
use Carbon\Carbon;

// Registering a simple inspire command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('schedule:tasks', function () {
   // Log::info('Starting schedule:tasks command');
   // Log::info('Effective User ID in console command: ' . posix_geteuid()); // Log the user ID

    // Define executeTask as a closure within this command
    $executeTask = function ($task) {
        Log::info("Executing task: {$task->id}");
        $instance = $task->instance;
        $instanceController = new InstanceController();

        try {
            switch ($task->action) {
                case 'start':
                    Log::info("Starting instance: {$instance->id}");
                    $instanceController->start(new \Illuminate\Http\Request(), $instance);
                    break;
                case 'stop':
                    Log::info("Stopping instance: {$instance->id}");
                    $instanceController->stop(new \Illuminate\Http\Request(), $instance);
                    break;
                case 'restart':
                    Log::info("Restarting instance: {$instance->id}");
                    $instanceController->restart(new \Illuminate\Http\Request(), $instance);
                    break;
            }
            Log::info("Task executed successfully: {$task->id}");
        } catch (\Exception $e) {
            Log::error("Task execution failed: {$task->id}", ['error' => $e->getMessage()]);
        }
    };

    try {
        $tasks = TaskSchedule::where('enabled', 1)->get();
       // Log::info('Tasks retrieved from the database', ['task_count' => $tasks->count()]);

        foreach ($tasks as $task) {
            $cronExpression = sprintf(
                '%s %s * * %s',
                implode(',', $task->minutes),
                implode(',', $task->hours),
                implode(',', $task->days)
            );

            $cron = CronExpression::factory($cronExpression);
            $nextRun = Carbon::instance($cron->getNextRunDate());
            $minutesUntilNextRun = Carbon::now()->diffInMinutes($nextRun);

            Log::info('Scheduling task', [
                'task_id' => $task->id,
                'cron_expression' => $cronExpression,
                'action' => $task->action,
                'instance_id' => $task->instance_id,
                'next_run_in_minutes' => $minutesUntilNextRun
            ]);

            // If the time to run is less than 1 minute, execute the task immediately
            if ($minutesUntilNextRun < 1) {
               // Log::info("Executing task immediately: {$task->id}");
                $executeTask($task);
            } else {
                // Schedule task execution
                Schedule::call(function () use ($executeTask, $task) {
                    $executeTask($task);
                })->name("task_{$task->id}")->cron($cronExpression)->withoutOverlapping();
            }
        }
    } catch (\Exception $e) {
        Log::error('Error in schedule:tasks command', ['error' => $e->getMessage()]);
    }
})->describe('Schedule tasks from the database');

// Define a task that runs every minute to trigger the above command
Schedule::command('schedule:tasks')->everyMinute();
Schedule::command('metrics:capture')->everyFiveSeconds();
Schedule::command('instance:check-status')->everyFiveSeconds();