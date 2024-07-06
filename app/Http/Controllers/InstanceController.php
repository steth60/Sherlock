<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstanceController extends Controller
{
    public function index()
    {
        $instances = Instance::all();
        return view('instances.index', compact('instances'));
    }

    public function create()
    {
        return view('instances.create');
    }

    public function store(Request $request)
    {
        Log::info('Store method called');

        $validated = $request->validate([
            'name' => 'required',
            'github_url' => 'required|url',
            'start_command' => 'required'
        ]);

        Log::info('Validation passed', $validated);

        $validated['status'] = 'stopped';  // Default status

        DB::beginTransaction();
        try {
            Log::info('Creating instance');
            $instance = Instance::create($validated);
            Log::info('Instance created', ['id' => $instance->id]);

            // Clone GitHub repository
            $this->cloneRepository($instance);

            DB::commit();
            Log::info('DB commit successful');

            return redirect()->route('instances.index')->with('success', 'Instance created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during store process', ['message' => $e->getMessage()]);

            // Optionally, delete the instance if setup fails
            if (isset($instance)) {
                $instance->delete();
            }

            return redirect()->route('instances.create')->withErrors(['error' => 'Failed to setup the instance: ' . $e->getMessage()]);
        }
    }

    public function show(Instance $instance)
    {
        $outputFile = base_path('instances/' . $instance->id . '/output.log');
        $output = '';

        // Check if the PID file exists and the process is running
        $pidFile = base_path('instances/' . $instance->id . '/process.pid');
        if (file_exists($pidFile)) {
            $pid = file_get_contents($pidFile);

            $commandCheck = 'ps -p ' . $pid;
            $checkProcess = Process::fromShellCommandline($commandCheck);
            $checkProcess->run();

            if (!$checkProcess->isSuccessful()) {
                // Process is not running, update status to stopped
                $instance->status = 'stopped';
                $instance->save();
                Log::info('Instance status updated to stopped as the process was not running.', ['pid' => $pid]);
                unlink($pidFile); // Remove the PID file as the process is not running
            }
        } else {
            // PID file does not exist, ensure status is stopped
            $instance->status = 'stopped';
            $instance->save();
            Log::warning('PID file does not exist, status set to stopped.', ['pid_file' => $pidFile]);
        }

        if (file_exists($outputFile)) {
            $output = file_get_contents($outputFile);
        }

        return view('instances.show', compact('instance', 'output'));
    }

    public function running()
    {
        $runningInstances = Instance::where('status', 'running')->get();
        return view('instances.running', compact('runningInstances'));
    }

    public function output(Instance $instance)
    {
        $outputFile = base_path('instances/' . $instance->id . '/output.log');
        if (file_exists($outputFile)) {
            $output = file_get_contents($outputFile);
        } else {
            $output = "No output yet.";
        }

        return response()->json(['output' => $output]);
    }

    public function start(Instance $instance)
    {
        $repoPath = base_path('instances/' . $instance->id);
        $venvPath = $repoPath . '/venv/bin/';
        $startCommand = $instance->start_command;

        $outputFile = $repoPath . '/output.log';
        $pidFile = $repoPath . '/process.pid';

        // Append the start trigger message with timestamp
        $startMessage = "[" . now()->format('d/m/y H:i') . "] CONSOLE: System trigger start\n";
        file_put_contents($outputFile, $startMessage, FILE_APPEND);

        // Build the full start command with nohup to run it in the background
        $command = 'nohup ' . $venvPath . 'python ' . $repoPath . '/' . $startCommand . ' >> ' . $outputFile . ' 2>&1 & echo $!';
        Log::info('Starting process', ['command' => $command]);

        // Run the process and capture the PID
        try {
            $process = Process::fromShellCommandline($command);
            $process->run();

            if ($process->isSuccessful()) {
                $pid = trim($process->getOutput());
                file_put_contents($pidFile, $pid);
                Log::info('Process started successfully', ['pid' => $pid]);

                // Save the PID to the database
                $instance->pid = $pid;
                $instance->status = 'running';
                $instance->save();
            } else {
                throw new ProcessFailedException($process);
            }
        } catch (ProcessFailedException $exception) {
            Log::error('Process failed to start', [
                'error' => $exception->getMessage(),
                'output' => $exception->getProcess()->getOutput(),
                'errorOutput' => $exception->getProcess()->getErrorOutput()
            ]);
            return redirect()->route('instances.index')->withErrors(['error' => 'Failed to start the instance: ' . $exception->getMessage()]);
        }

        return redirect()->route('instances.index')->with('success', 'Instance started successfully.');
    }

    public function stop(Instance $instance)
    {
        $repoPath = base_path('instances/' . $instance->id);
        $outputFile = $repoPath . '/output.log';
        $pidFile = $repoPath . '/process.pid';

        // Append the stop trigger message with timestamp
        $stopMessage = "[" . now()->format('d/m/y H:i') . "] CONSOLE: System trigger stop\n";
        file_put_contents($outputFile, $stopMessage, FILE_APPEND);

        // Logic to stop the script
        if ($instance->pid) {
            $pid = $instance->pid;

            // Check if the process is running
            $commandCheck = 'ps -p ' . $pid;
            $checkProcess = Process::fromShellCommandline($commandCheck);
            $checkProcess->run();

            if ($checkProcess->isSuccessful()) {
                // Use the kill command to stop the process
                $command = 'kill ' . $pid;
                Log::info('Stopping process', ['command' => $command]);

                try {
                    $process = Process::fromShellCommandline($command);
                    $process->mustRun();
                    Log::info('Process stopped successfully');
                    unlink($pidFile); // Remove the PID file after stopping the process

                    // Clear the PID in the database
                    $instance->pid = null;
                    $instance->status = 'stopped';
                    $instance->save();

                    return redirect()->route('instances.index')->with('success', 'Instance stopped successfully.');
                } catch (ProcessFailedException $exception) {
                    Log::error('Process failed to stop', [
                        'error' => $exception->getMessage(),
                        'output' => $exception->getProcess()->getOutput(),
                        'errorOutput' => $exception->getProcess()->getErrorOutput()
                    ]);
                    return redirect()->route('instances.index')->withErrors(['error' => 'Failed to stop the instance: ' . $exception->getMessage()]);
                }
            } else {
                Log::warning('No such process found', ['pid' => $pid]);

                // Clear the PID in the database since the process is not running
                $instance->pid = null;
                $instance->status = 'stopped';
                $instance->save();

                return redirect()->route('instances.index')->with('success', 'Instance status updated to stopped as the process was not running.');
            }
        } else {
            Log::warning('PID not found in database for instance', ['instance_id' => $instance->id]);

            // Update instance status to stopped since the PID is missing
            $instance->status = 'stopped';
            $instance->save();

            return redirect()->route('instances.index')->withErrors(['error' => 'Failed to stop the instance: PID not found.']);
        }
    }

    public function delete(Instance $instance)
    {
        $repoPath = base_path('instances/' . $instance->id);

        // Delete the repository folder
        $this->deleteDirectory($repoPath);

        // Delete the instance from the database
        $instance->delete();

        return redirect()->route('instances.index');
    }

    public function restart(Instance $instance)
    {
        $repoPath = base_path('instances/' . $instance->id);
        $outputFile = $repoPath . '/output.log';
        $pidFile = $repoPath . '/process.pid';

        // Append the restart trigger message with timestamp
        $restartMessage = "[" . now()->format('d/m/y H:i') . "] CONSOLE: System trigger restart\n";
        file_put_contents($outputFile, $restartMessage, FILE_APPEND);

        // Stop the script if it's running
        if ($instance->pid) {
            $pid = $instance->pid;

            // Check if the process is running
            $commandCheck = 'ps -p ' . $pid;
            $checkProcess = Process::fromShellCommandline($commandCheck);
            $checkProcess->run();

            if ($checkProcess->isSuccessful()) {
                // Use the kill command to stop the process
                $command = 'kill ' . $pid;
                Log::info('Stopping process for restart', ['command' => $command]);

                try {
                    $process = Process::fromShellCommandline($command);
                    $process->mustRun();
                    Log::info('Process stopped successfully for restart');
                    unlink($pidFile); // Remove the PID file after stopping the process
                } catch (ProcessFailedException $exception) {
                    Log::error('Process failed to stop for restart', [
                        'error' => $exception->getMessage(),
                        'output' => $exception->getProcess()->getOutput(),
                        'errorOutput' => $exception->getProcess()->getErrorOutput()
                    ]);
                    return redirect()->route('instances.index')->withErrors(['error' => 'Failed to restart the instance: ' . $exception->getMessage()]);
                }
            } else {
                Log::warning('No such process found for restart', ['pid' => $pid]);
                unlink($pidFile); // Remove the PID file if the process is not found
            }
        }

        // Now start the script again
        $venvPath = $repoPath . '/venv/bin/';
        $startCommand = $instance->start_command;

        // Append the start trigger message with timestamp
        $startMessage = "[" . now()->format('d/m/y H:i') . "] CONSOLE: System trigger start\n";
        file_put_contents($outputFile, $startMessage, FILE_APPEND);

        // Build the full start command with nohup to run it in the background
        $command = 'nohup ' . $venvPath . 'python ' . $repoPath . '/' . $startCommand . ' >> ' . $outputFile . ' 2>&1 & echo $!';
        Log::info('Starting process for restart', ['command' => $command]);

        // Run the process and capture the PID
        try {
            $process = Process::fromShellCommandline($command);
            $process->run();

            if ($process->isSuccessful()) {
                $pid = trim($process->getOutput());
                file_put_contents($pidFile, $pid);
                Log::info('Process started successfully for restart', ['pid' => $pid]);

                // Save the PID to the database
                $instance->pid = $pid;
                $instance->status = 'running';
                $instance->save();
            } else {
                throw new ProcessFailedException($process);
            }
        } catch (ProcessFailedException $exception) {
            Log::error('Process failed to start for restart', [
                'error' => $exception->getMessage(),
                'output' => $exception->getProcess()->getOutput(),
                'errorOutput' => $exception->getProcess()->getErrorOutput()
            ]);
            return redirect()->route('instances.index')->withErrors(['error' => 'Failed to restart the instance: ' . $exception->getMessage()]);
        }

        return redirect()->route('instances.index')->with('success', 'Instance restarted successfully.');
    }

    private function cloneRepository(Instance $instance)
    {
        Log::info('cloneRepository called', ['instance' => $instance]);

        $repoPath = base_path('instances/' . $instance->id);

        // Check if the directory exists and is not empty
        if (is_dir($repoPath) && (new \FilesystemIterator($repoPath))->valid()) {
            Log::info('Directory exists, deleting it');
            $this->deleteDirectory($repoPath);
        }

        $process = new Process(['git', 'clone', $instance->github_url, $repoPath]);
        try {
            $process->mustRun();
            Log::info('Git clone successful');

            // Create virtual environment
            $process = new Process(['python3', '-m', 'venv', $repoPath . '/venv']);
            $process->mustRun();
            Log::info('Virtual environment created');

            // Check if requirements.txt exists
            $requirementsFile = $repoPath . '/requirements.txt';
            if (file_exists($requirementsFile)) {
                // Install dependencies
                $process = new Process([$repoPath . '/venv/bin/pip', 'install', '-r', $requirementsFile]);
                $process->mustRun();
                Log::info('Dependencies installed');
            } else {
                Log::info('No requirements.txt file found, skipping dependency installation');
            }
        } catch (ProcessFailedException $exception) {
            Log::error('Process failed', ['message' => $exception->getMessage()]);
            throw $exception;
        }
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!is_link($dir . '/' . $item) && is_dir($dir . '/' . $item)) {
                $this->deleteDirectory($dir . '/' . $item);
            } else {
                unlink($dir . '/' . $item);
            }
        }

        return rmdir($dir);
    }
}
