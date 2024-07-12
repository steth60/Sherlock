<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use App\Models\Note;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class InstanceController extends Controller
{
    private $logLineLimit = 500;
    private $archiveFolder = 'instances/archive/';
    private $maxArchivedFiles = 10;

    /**
     * Index of content:
     * 1. CRUD Operations
     * 2. Instance Management
     * 3. File Operations
     * 4. Environment Variables
     * 5. Notes Management
     * 6. Metrics and Logging
     * 7. Repository Operations
     * 8. Helper Methods
     */

    // 1. CRUD Operations

    /**
     * Display a listing of the instances.
     */
    public function index()
    {
        $instances = Instance::all();
        return view('instances.index', compact('instances'));
    }

    /**
     * Show the form for creating a new instance.
     */
    public function create()
    {
        return view('instances.create');
    }

    /**
     * Store a newly created instance in storage.
     */
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

    /**
     * Display the specified instance.
     */
    public function show(Instance $instance)
    {
        $instance->load('schedules');
    
        $outputFile = base_path('instances/' . $instance->id . '/output.log');
        $output = '';
    
        if (file_exists($outputFile)) {
            $output = file_get_contents($outputFile);
        }
        
        $envFilePath = base_path('instances/' . $instance->id . '/.env');
    
        // Create .env file if it doesn't exist
        if (!File::exists($envFilePath)) {
            File::put($envFilePath, '');
        }
    
        $envContent = File::exists($envFilePath) ? File::get($envFilePath) : '';
    
        $notes = $instance->notes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        return view('instances.show', compact('instance', 'output', 'envContent', 'notes'));
    }

    /**
     * Show the form for editing the specified instance.
     */
    public function edit(Instance $instance)
    {
        return view('instances.edit', compact('instance'));
    }

    /**
     * Update the specified instance in storage.
     */
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

    /**
     * Remove the specified instance from storage.
     */
    public function delete(Instance $instance)
    {
        $repoPath = base_path('instances/' . $instance->id);

        // Delete the repository folder
        $this->deleteDirectory($repoPath);

        // Delete the instance from the database
        $instance->delete();

        return redirect()->route('instances.index');
    }

    // 2. Instance Management

    /**
     * Display a listing of running instances.
     */
    public function running()
    {
        $runningInstances = Instance::where('status', 'running')->get();
        return view('instances.running', compact('runningInstances'));
    }

    public function getStatus(Instance $instance)
    {
    return response()->json([
        'status' => $instance->status,
    ]);
    }
    /**
     * Start the specified instance.
     */
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
        $this->storeLog($instance, $startMessage);
    
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

                    // Start capturing CPU and memory usage
                    $this->captureMetrics($instance);
    
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

    /**
     * Stop the specified instance.
     */
    public function stop(Request $request, Instance $instance)
    {
        Log::info("Instance stop method called for instance: {$instance->id}");
        Log::info('Effective User ID in stop method: ' . posix_geteuid()); // Log the user ID
        
        $repoPath = base_path('instances/' . $instance->id);
        $outputFile = $repoPath . '/output.log';
        $pidFile = $repoPath . '/process.pid';
        
        // Append the stop trigger message with timestamp
        $stopMessage = "[" . now()->format('d/m/y H:i') . "] CONSOLE: System trigger stop\n";
        $this->storeLog($instance, $stopMessage);
        
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

    /**
     * Restart the specified instance.
     */
    public function restart(Request $request, Instance $instance)
    {
        Log::info("Instance restart method called for instance: {$instance->id}");
        $stopResponse = $this->stop($request, $instance);

        if ($stopResponse->getStatusCode() !== 200) {
            return $stopResponse;
        }

        return $this->start($request, $instance);
    }

    /**
     * Display the instance output.
     */
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

    // 3. File Operations

    /**
     * List files for the specified instance.
     */
    public function listFiles(Instance $instance, Request $request)
    {
        $instancePath = base_path('instances/' . $instance->id);
        $currentPath = $request->get('path', '');
        $fullPath = $instancePath . '/' . $currentPath;
        
        $files = $this->getFiles($fullPath);
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        
        usort($files, function($a, $b) use ($sortBy, $sortOrder) {
            $aValue = $this->getFileProperty($a, $sortBy);
            $bValue = $this->getFileProperty($b, $sortBy);
            
            if ($sortOrder === 'asc') {
                return $aValue <=> $bValue;
            } else {
                return $bValue <=> $aValue;
            }
        });
    
        return view('instances.partials.file-browser', compact('instance', 'files', 'currentPath', 'sortBy', 'sortOrder'));
    }

    /**
     * View the content of a file.
     */
    public function viewFile(Instance $instance, Request $request)
    {
        $filePath = $request->input('file');
        $fullPath = base_path('instances/' . $instance->id . '/' . $filePath);
        
        if (!file_exists($fullPath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        $content = file_get_contents($fullPath);
        return response()->json(['content' => $content]);
    }

    /**
     * Update the content of a file.
     */
    public function updateFile(Instance $instance, Request $request)
    {
        $filePath = $request->input('file');
        $content = $request->input('content');
        $fullPath = base_path('instances/' . $instance->id . '/' . $filePath);
        
        if (!file_exists($fullPath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        file_put_contents($fullPath, $content);
        return response()->json(['message' => 'File updated successfully.']);
    }

    /**
     * Display the file editor.
     */
    public function fileEditor(Request $request, Instance $instance)
    {
        $filePath = $request->input('file');
        $content = $request->input('content');
        $fileId = $request->input('fileId');

        return view('instances.partials.file-editor', compact('filePath', 'content', 'fileId'));
    }

    // 4. Environment Variables

    /**
     * Get the environment variables for the specified instance.
     */
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

    /**
     * Update the environment variables for the specified instance.
     */
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

    // 5. Notes Management

    /**
     * Store a new note for the specified instance.
     */
    public function storeNote(Request $request, Instance $instance)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $note = $instance->notes()->create([
            'content' => strip_tags($validated['content']),
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'id' => $note->id,
            'content' => $note->content,
            'created_at' => $note->created_at->toDateTimeString(),
            'user_name' => Auth::user()->name,
        ]);
    }

    /**
     * Delete a note.
     */
    public function destroyNote(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $note->delete();
        return response()->json(['message' => 'Note deleted successfully']);
    }

    /**
     * Get notes for the specified instance.
     */
    public function getNotes(Instance $instance)
    {
        $notes = $instance->notes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($notes);
    }

    // 6. Metrics and Logging

    /**
     * Capture metrics for the specified instance.
     */
    public function captureMetrics($instance)
    {
        $repoPath = base_path('instances/' . $instance->id);
        $metricsFile = $repoPath . '/metrics.log';
        $pidFile = $repoPath . '/process.pid';
    
        if (file_exists($pidFile)) {
            $pid = file_get_contents($pidFile);
            $command = "ps -p $pid -o %cpu,%mem,etime --no-headers";
            $metricsCommand = Process::fromShellCommandline($command);
            $metricsCommand->run();
    
            if ($metricsCommand->isSuccessful()) {
                $output = $metricsCommand->getOutput();
                $output = "[" . now()->format('Y-m-d H:i:s') . "] " . trim($output) . "\n";
                file_put_contents($metricsFile, $output, FILE_APPEND);
                $this->limitLines($metricsFile, 100); // Limit to 100 lines
            } else {
                Log::error('Failed to capture metrics', [
                    'error' => $metricsCommand->getErrorOutput()
                ]);
            }
        }
    }
    
    /**
     * Get metrics for the specified instance.
     */
    public function getMetrics(Instance $instance)
    {
        if ($instance->status !== 'running') {
            return response()->json([
                'cpu' => [],
                'memory' => [],
                'uptime' => '0:00.00'
            ]);
        }
    
        $metricsFile = base_path('instances/' . $instance->id . '/metrics.log');
        $metricsData = [
            'cpu' => [0],
            'memory' => [0],
            'uptime' => '0:00.00'
        ];
    
        if (file_exists($metricsFile)) {
            $lines = file($metricsFile, FILE_IGNORE_NEW_LINES);
            $lines = array_slice($lines, -20); // Get last 20 entries
    
            foreach ($lines as $line) {
                preg_match('/\[(.*?)\]\s+(\d+\.?\d*)\s+(\d+\.?\d*)\s+(\S+)/', $line, $matches);
                if (count($matches) === 5) {
                    $metricsData['cpu'][] = (float) $matches[2];
                    $metricsData['memory'][] = (float) $matches[3];
                    $metricsData['uptime'] = $matches[4]; // This will be overwritten each time, keeping the latest
                }
            }
        }
    
        return response()->json($metricsData);
    }

    // 7. Repository Operations

    /**
     * Show the update page for the specified instance.
     */
    public function showUpdatePage(Instance $instance)
    {
        return view('instances.update', compact('instance'));
    }

    /**
     * Check for updates for the specified instance.
     */
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

    /**
     * Confirm and apply updates for the specified instance.
     */
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

    // 8. Helper Methods

    /**
     * Clone the repository for the specified instance.
     */
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

    /**
     * Delete a directory and its contents.
     */
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

    /**
     * Get files in a directory.
     */
    private function getFiles($dir)
    {
        $files = [];
        $scan = scandir($dir);
        foreach ($scan as $file) {
            if ($file !== '.' && $file !== '..') {
                $fullPath = $dir . '/' . $file;
                $files[] = [
                    'name' => $file,
                    'path' => $fullPath,
                    'is_dir' => is_dir($fullPath),
                    'size' => is_dir($fullPath) ? '' : filesize($fullPath),
                    'modified' => filemtime($fullPath),
                    'created' => filectime($fullPath),
                    'owner' => posix_getpwuid(fileowner($fullPath))['name']
                ];
            }
        }
        return $files;
    }

    /**
     * Get a file property for sorting.
     */
    private function getFileProperty($file, $property)
    {
        switch ($property) {
            case 'name':
                return strtolower($file['name']);
            case 'size':
                return $file['size'];
            case 'modified':
                return $file['modified'];
            case 'created':
                return $file['created'];
            default:
                return $file['name'];
        }
    }

    /**
     * Limit the number of lines in a file.
     */
    private function limitLines($filePath, $maxLines)
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
    
        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, count($lines) - $maxLines);
            file_put_contents($filePath, implode("\n", $lines) . "\n");
        }
    }

    /**
     * Store a log entry for the specified instance.
     */
    private function storeLog(Instance $instance, $logEntry)
    {
        $logFile = base_path('instances/' . $instance->id . '/output.log');
        $archiveFolder = base_path($this->archiveFolder . $instance->id);

        // Ensure the archive folder exists
        if (!File::exists($archiveFolder)) {
            File::makeDirectory($archiveFolder, 0755, true);
        }

        // Rotate log if needed
        $this->rotateLog($logFile, $archiveFolder);

        // Append log entry
        File::append($logFile, $logEntry . PHP_EOL);
    }

    /**
     * Rotate the log file if it exceeds the line limit.
     */
    private function rotateLog($logFile, $archiveFolder)
    {
        if (File::exists($logFile)) {
            $lineCount = count(file($logFile));

            if ($lineCount >= $this->logLineLimit) {
                $timestamp = now()->format('YmdHis');
                $archivedLogFile = $archiveFolder . '/output_' . $timestamp . '.log';

                // Move current log to archive
                File::move($logFile, $archivedLogFile);

                // Check if we need to compress old logs
                $this->compressOldLogs($archiveFolder);
            }
        }
    }

    /**
     * Compress old log files if the number of archived files exceeds the limit.
     */
    private function compressOldLogs($archiveFolder)
    {
        $logFiles = File::files($archiveFolder);

        if (count($logFiles) >= $this->maxArchivedFiles) {
            $zipFile = $archiveFolder . '/logs_' . now()->format('YmdHis') . '.zip';
            $zip = new ZipArchive;

            if ($zip->open($zipFile, ZipArchive::CREATE) === true) {
                foreach ($logFiles as $logFile) {
                    $zip->addFile($logFile->getRealPath(), $logFile->getFilename());
                }
                $zip->close();

                // Delete the old log files after zipping
                foreach ($logFiles as $logFile) {
                    File::delete($logFile);
                }
            }
        }
    }
}