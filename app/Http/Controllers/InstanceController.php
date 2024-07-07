<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;


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
        $instance->load('schedules');
    
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
        
        $envFilePath = base_path('instances/' . $instance->id . '/.env');
    
        // Create .env file if it doesn't exist
        if (!File::exists($envFilePath)) {
            File::put($envFilePath, '');
        }
    
        $envContent = File::exists($envFilePath) ? File::get($envFilePath) : '';
    
        return view('instances.show', compact('instance', 'output', 'envContent'));
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

    public function start(Request $request, Instance $instance)
    {
        Log::info("Instance start method called for instance: {$instance->id}");
        $repoPath = base_path('instances/' . $instance->id);
        $venvPath = $repoPath . '/venv/bin/';
        $startCommand = $instance->start_command;
    
        $outputFile = $repoPath . '/output.log';
        $pidFile = $repoPath . '/process.pid';
    
        // Append the start trigger message with timestamp
        $startMessage = "[" . now()->format('d/m/y H:i') . "] CONSOLE: System trigger start\n";
        file_put_contents($outputFile, $startMessage, FILE_APPEND);
    
        // Build the full start command with setsid to create a new session and process group
        $command = 'setsid ' . $venvPath . 'python ' . $repoPath . '/' . $startCommand . ' >> ' . $outputFile . ' 2>&1 & echo $!';
        Log::info('Starting process', ['command' => $command]);
    
        // Run the process and capture the PID
        try {
            $process = Process::fromShellCommandline($command);
            $process->run();
    
            if ($process->isSuccessful()) {
                $pid = trim($process->getOutput());
                Log::info('Process output', ['output' => $pid]);
    
                if (!empty($pid)) {
                    file_put_contents($pidFile, $pid);
                    Log::info('PID file created successfully', ['pid_file' => $pidFile, 'pid' => $pid]);
    
                    // Save the PID to the database
                    $instance->pid = $pid;
                    $instance->status = 'running';
                    $instance->save();
    
                    return response()->json(['status' => 'success', 'message' => 'Instance started successfully.', 'instance' => $instance]);
                } else {
                    Log::warning('No PID captured from process output', ['output' => $pid]);
                    return response()->json(['status' => 'error', 'message' => 'Failed to capture PID from process output.'], 500);
                }
            } else {
                throw new ProcessFailedException($process);
            }
        } catch (ProcessFailedException $exception) {
            Log::error('Process failed to start', [
                'error' => $exception->getMessage(),
                'output' => $exception->getProcess()->getOutput(),
                'errorOutput' => $exception->getProcess()->getErrorOutput()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Failed to start the instance: ' . $exception->getMessage()], 500);
        }
    }
    
    public function stop(Request $request, Instance $instance)
    {
        Log::info("Instance stop method called for instance: {$instance->id}");
        Log::info('Effective User ID in stop method: ' . posix_geteuid()); // Log the user ID
        
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
        
            Log::info("Checking process status", ['pid' => $pid, 'is_running' => $checkProcess->isSuccessful()]);
        
            if ($checkProcess->isSuccessful()) {
                // Attempt to stop the process group
                $command = 'kill -- -' . $pid;
                Log::info('Stopping process group', ['command' => $command]);
        
                try {
                    $process = Process::fromShellCommandline($command);
                    $process->run();
    
                    // Check if the process was successfully killed
                    $commandCheckAgain = 'ps -p ' . $pid;
                    $checkProcessAgain = Process::fromShellCommandline($commandCheckAgain);
                    $checkProcessAgain->run();
    
                    if (!$checkProcessAgain->isSuccessful()) {
                        Log::info('Process group stopped successfully');
                    } else {
                        Log::warning('Process still running after kill attempt');
                        throw new ProcessFailedException($process);
                    }
                } catch (ProcessFailedException $exception) {
                    Log::error('Process group failed to stop', [
                        'error' => $exception->getMessage(),
                        'output' => $exception->getProcess()->getOutput(),
                        'errorOutput' => $exception->getProcess()->getErrorOutput(),
                    ]);
                    return response()->json(['status' => 'error', 'message' => 'Failed to stop the instance: ' . $exception->getMessage()], 500);
                }
                
                if (File::exists($pidFile)) {
                    File::delete($pidFile);
                    Log::info('PID file deleted successfully', ['pid_file' => $pidFile]);
                }
        
                // Clear the PID in the database
                $instance->pid = null;
                $instance->status = 'stopped';
                $instance->save();
        
                return response()->json(['status' => 'success', 'message' => 'Instance stopped successfully.', 'instance' => $instance]);
            } else {
                Log::warning('No such process found', ['pid' => $pid]);
        
                // Clear the PID in the database since the process is not running
                $instance->pid = null;
                $instance->status = 'stopped';
                $instance->save();
        
                return response()->json(['status' => 'success', 'message' => 'Instance status updated to stopped as the process was not running.', 'instance' => $instance]);
            }
        } else {
            Log::warning('PID not found in database for instance', ['instance_id' => $instance->id]);
        
            // Update instance status to stopped since the PID is missing
            $instance->status = 'stopped';
            $instance->save();
        
            return response()->json(['status' => 'error', 'message' => 'Failed to stop the instance: PID not found.'], 500);
        }
    }
    
    

    public function restart(Request $request, Instance $instance)
    {   
        Log::info("Instance restart method called for instance: {$instance->id}");
        $stopResponse = $this->stop($request, $instance);

        if ($stopResponse->getStatusCode() !== 200) {
            return $stopResponse;
        }

        return $this->start($request, $instance);
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

    public function showUpdatePage(Instance $instance)
    {
        return view('instances.update', compact('instance'));
    }

    public function checkUpdates(Request $request, Instance $instance)
    {
        $repoPath = base_path('instances/' . $instance->id);

        // Fetch updates from the remote repository
        $process = new Process(['git', 'fetch'], $repoPath);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch updates.']);
        }

        // Check for differences
        $process = new Process(['git', 'diff', 'HEAD..origin/main'], $repoPath);
        $process->run();

        if ($process->isSuccessful()) {
            $output = $process->getOutput();
            if (empty(trim($output))) {
                return response()->json(['status' => 'up-to-date', 'message' => 'No updates available.']);
            } else {
                return response()->json(['status' => 'updates-available', 'diff' => $output]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to check for updates.']);
        }
    }

    public function confirmUpdates(Request $request, Instance $instance)
    {
        $repoPath = base_path('instances/' . $instance->id);

        // Pull the latest updates
        $process = new Process(['git', 'pull'], $repoPath);
        $process->run();

        if ($process->isSuccessful()) {
            return response()->json(['status' => 'success', 'message' => 'Updates pulled successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to pull updates.']);
        }
    }
    public function edit(Instance $instance)
    {
        return view('instances.edit', compact('instance'));
    }

    public function update(Request $request, Instance $instance)
    {
        $validated = $request->validate([
            'github_url' => 'required|url',
            'start_command' => 'required',
            'description' => 'nullable|string'
        ]);

        $instance->update($validated);

        return redirect()->route('instances.show', $instance)->with('success', 'Instance updated successfully.');
    }

    public function getEnv(Instance $instance)
    {
        $envFilePath = base_path('instances/' . $instance->id . '/.env');
        
        // Create .env file if it doesn't exist
        if (!File::exists($envFilePath)) {
            File::put($envFilePath, '');
        }

        $envContent = File::exists($envFilePath) ? File::get($envFilePath) : '';

        return view('instances.show', compact('instance', 'envContent'));
    }

    public function updateEnv(Request $request, Instance $instance)
    {
        $envData = $request->input('env');
        $envFilePath = base_path('instances/' . $instance->id . '/.env');
    
        $envContent = '';
        foreach ($envData as $env) {
            if (isset($env['type']) && $env['type'] === 'variable') {
                $key = $env['key'] ?? '';
                $value = $env['value'] ?? '';
                $envContent .= "{$key}={$value}\n";
            } elseif (isset($env['type']) && $env['type'] === 'comment') {
                $comment = $env['comment'] ?? '';
                $envContent .= "# {$comment}\n";
            }
        }
    
        File::put($envFilePath, $envContent);
    
        return redirect()->route('instances.show', $instance)->with('success', 'Environment variables updated successfully.');
    }
    
    
}
