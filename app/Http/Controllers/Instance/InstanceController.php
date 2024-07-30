<?php

namespace App\Http\Controllers\Instance;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use App\Events\ConsoleOutputUpdated;
use Carbon\Carbon;
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
    private $logLineLimit = 5000;
    private $archiveFolder = 'instances/archive/';
    private $maxArchivedFiles = 10;
    private $lastReadOffset = 0;

    // 1. CRUD Operations

    public function index()
    {
        $totalInstances = Instance::count();
        $runningInstances = Instance::where('status', 'running')->count();
        $stoppedInstances = Instance::where('status', 'stopped')->count();
        $recentInstances = Instance::latest()->take(5)->get();

        return view('instances.index', compact('totalInstances', 'runningInstances', 'stoppedInstances', 'recentInstances'));
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

    public function start(Request $request, Instance $instance)
    {
        Log::info("Instance start method called for instance: {$instance->id}");
        $repoPath = base_path('instances/' . $instance->id);
        $venvPath = $repoPath . '/venv/bin/';
        $startCommand = $instance->start_command;
    
        $outputFile = $repoPath . '/output.log';
        $pidFile = $repoPath . '/process.pid';
    
        $startMessage = "[" . now()->format('d/m/y H:i') . "] CONSOLE: System trigger start\n";
        $this->storeLog($instance, $startMessage);
    
        $command = 'setsid ' . $venvPath . 'python ' . $repoPath . '/' . $startCommand . ' >> ' . $outputFile . ' 2>&1 & echo $!';
        Log::info('Starting process', ['command' => $command]);
    
        try {
            $process = Process::fromShellCommandline($command);
            $process->run();
    
            if ($process->isSuccessful()) {
                $pid = trim($process->getOutput());
                Log::info('Process output', ['output' => $pid]);
    
                if (!empty($pid)) {
                    file_put_contents($pidFile, $pid);
                    Log::info('PID file created successfully', ['pid_file' => $pidFile, 'pid' => $pid]);
    
                    $instance->pid = $pid;
                    $instance->status = 'running';
                    $instance->start_time = now();
                    $instance->save();
    
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
    
    public function stop(Request $request, Instance $instance)
    {
        Log::info("Instance stop method called for instance: {$instance->id}");
        $repoPath = base_path('instances/' . $instance->id);
        $pidFile = $repoPath . '/process.pid';
    
        $stopMessage = "[" . now()->format('d/m/y H:i') . "] CONSOLE: System trigger stop\n";
        $this->storeLog($instance, $stopMessage);
    
        if ($instance->pid) {
            $pid = $instance->pid;
    
            $commandCheck = 'ps -p ' . $pid;
            $checkProcess = Process::fromShellCommandline($commandCheck);
            $checkProcess->run();
    
            if ($checkProcess->isSuccessful()) {
                $command = 'kill -- -' . $pid;
                Log::info('Stopping process group', ['command' => $command]);
    
                try {
                    $process = Process::fromShellCommandline($command);
                    $process->run();
    
                    $commandCheckAgain = 'ps -p ' . $pid;
                    $checkProcessAgain = Process::fromShellCommandline($commandCheckAgain);
                    $checkProcessAgain->run();
    
                    if (!$checkProcessAgain->isSuccessful()) {
                        Log::info('Process group stopped successfully');
                    } else {
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
    
                $instance->pid = null;
                $instance->status = 'stopped';
                $instance->start_time = null;
                $instance->save();
    
                return response()->json(['status' => 'success', 'message' => 'Instance stopped successfully.', 'instance' => $instance]);
            } else {
                $instance->pid = null;
                $instance->status = 'stopped';
                $instance->start_time = null;
                $instance->save();
    
                return response()->json(['status' => 'success', 'message' => 'Instance status updated to stopped as the process was not running.', 'instance' => $instance]);
            }
        } else {
            $instance->status = 'stopped';
            $instance->start_time = null;
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
    
        return view('instances.partials.file-browser-tab', compact('instance', 'files', 'currentPath', 'sortBy', 'sortOrder'));
    }

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

    public function fileEditor(Request $request, Instance $instance)
    {
        $filePath = $request->input('file');
        $content = $request->input('content');
        $fileId = $request->input('fileId');

        return view('instances.partials.file-editor', compact('filePath', 'content', 'fileId'));
    }

    // 4. Environment Variables

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

    // 5. Notes Management

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

    public function destroyNote($instanceId, Note $note)
    {
        // Ensure the note belongs to the instance
        if ($note->instance_id !== (int) $instanceId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Ensure the user is authorized to delete the note
        if (auth()->id() !== $note->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        try {
            $note->delete();
            return response()->json(['message' => 'Note deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete note.'], 500);
        }
    }

    public function getNotes(Instance $instance)
    {
        $notes = $instance->notes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($notes);
    }

    // 6. Metrics and Logging

    public function captureMetrics($instance)
    {
        $repoPath = base_path('instances/' . $instance->id);
        $metricsFile = $repoPath . '/metrics.log';
        $pidFile = $repoPath . '/process.pid';
        $tmuxOutputFile = $repoPath . '/output.log';

        if (file_exists($pidFile)) {
            $pid = file_get_contents($pidFile);
            $command = "ps -p $pid -o %cpu,%mem,etime --no-headers";
            $metricsCommand = Process::fromShellCommandline($command);
            $metricsCommand->run();

            if ($metricsCommand->isSuccessful()) {
                $output = trim($metricsCommand->getOutput());
                $formattedOutput = "[" . now()->format('Y-m-d H:i:s') . "] " . $output . "\n";
                file_put_contents($metricsFile, $formattedOutput, FILE_APPEND);
                $this->limitLines($metricsFile, 100); // Limit to 100 lines
            } else {
                Log::error('Failed to capture metrics', [
                    'error' => $metricsCommand->getErrorOutput()
                ]);
            }
        }

        // Read from tmux output file
        if (file_exists($tmuxOutputFile)) {
            $this->broadcastNewOutput($instance, $tmuxOutputFile);
        }
    }

    private function broadcastNewOutput($instance, $filePath)
    {
        clearstatcache(false, $filePath);
        $fileSize = filesize($filePath);

        if ($fileSize > $this->lastReadOffset) {
            $file = fopen($filePath, 'r');
            fseek($file, $this->lastReadOffset);

            while ($line = fgets($file)) {
                broadcast(new ConsoleOutputUpdated($instance->id, $line));
            }

            $this->lastReadOffset = ftell($file);
            fclose($file);
        }
    }

    public function getMetrics(Instance $instance)
    {
        if ($instance->status !== 'running') {
            return response()->json([
                'cpu' => [],
                'memory' => [],
                'uptime' => '0:00:00'
            ]);
        }
    
        $metricsFile = base_path('instances/' . $instance->id . '/metrics.log');
        $metricsData = [
            'cpu' => [],
            'memory' => [],
            'uptime' => '0:00:00'
        ];
    
        if (file_exists($metricsFile)) {
            $lines = file($metricsFile, FILE_IGNORE_NEW_LINES);
            $lines = array_slice($lines, -20); // Get last 20 entries
    
            foreach ($lines as $line) {
                preg_match('/\[(.*?)\]\s+(\d+\.?\d*)\s+(\d+\.?\d*)\s+(\S+)/', $line, $matches);
                if (count($matches) === 5) {
                    $metricsData['cpu'][] = (float) $matches[2];
                    $metricsData['memory'][] = (float) $matches[3];
                }
            }
        }
    
        // Calculate uptime
        if ($instance->start_time) {
            $startTime = Carbon::parse($instance->start_time);
            $currentTime = now();
            $uptime = $currentTime->diff($startTime);
    
            $metricsData['uptime'] = sprintf('%02d:%02d:%02d', $uptime->h, $uptime->i, $uptime->s);
        }
    
        return response()->json($metricsData);
    }

    // 7. Repository Operations

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
        $venvPath = $repoPath . '/venv/bin/';
        $requirementsFile = $repoPath . '/requirements.txt';
        $backupPath = base_path('instances/' . $instance->id . '/backup_' . now()->format('YmdHis'));

        try {
            // Create backup folder
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // Move all files except .env and backup directories to the backup folder
            foreach (File::files($repoPath) as $file) {
                if (basename($file) !== '.env') {
                    File::move($file, $backupPath . '/' . basename($file));
                }
            }

            foreach (File::directories($repoPath) as $directory) {
                $dirName = basename($directory);
                if ($dirName !== 'venv' && strpos($dirName, 'backup_') !== 0) {
                    File::moveDirectory($directory, $backupPath . '/' . $dirName);
                }
            }

            // Fetch the latest updates from GitHub
            $fetchProcess = new Process(['git', 'fetch', '--all'], $repoPath);
            $fetchProcess->run();

            if (!$fetchProcess->isSuccessful()) {
                throw new \Exception('Failed to fetch updates: ' . $fetchProcess->getErrorOutput());
            }

            // Reset to the latest version from the main branch
            $resetProcess = new Process(['git', 'reset', '--hard', 'origin/main'], $repoPath);
            $resetProcess->run();

            if ($resetProcess->isSuccessful()) {
                Log::info('Updates pulled and reset successfully for instance: ' . $instance->id);

                // Check if requirements.txt exists and update the virtual environment
                if (file_exists($requirementsFile)) {
                    try {
                        Log::info('Updating virtual environment for instance: ' . $instance->id);

                        $updateProcess = new Process([$venvPath . 'pip', 'install', '-r', $requirementsFile]);
                        $updateProcess->run();

                        if ($updateProcess->isSuccessful()) {
                            Log::info('Virtual environment updated successfully for instance: ' . $instance->id);
                            return response()->json(['status' => 'success', 'message' => 'Updates pulled and dependencies installed successfully.']);
                        } else {
                            throw new ProcessFailedException($updateProcess);
                        }
                    } catch (ProcessFailedException $exception) {
                        Log::error('Failed to update virtual environment for instance: ' . $instance->id, [
                            'error' => $exception->getMessage(),
                            'output' => $exception->getProcess()->getOutput(),
                            'errorOutput' => $exception->getProcess()->getErrorOutput(),
                        ]);
                        return response()->json(['status' => 'error', 'message' => 'Updates pulled, but failed to install dependencies: ' . $exception->getMessage()], 500);
                    }
                }

                return response()->json(['status' => 'success', 'message' => 'Updates pulled successfully.']);
            } else {
                throw new \Exception('Failed to reset to the latest version: ' . $resetProcess->getErrorOutput());
            }
        } catch (\Exception $e) {
            Log::error('Error during update process for instance: ' . $instance->id, [
                'message' => $e->getMessage()
            ]);

            // Rollback: Move files back from backup folder in case of any exception
            foreach (File::files($backupPath) as $file) {
                File::move($file, $repoPath . '/' . basename($file));
            }
            foreach (File::directories($backupPath) as $directory) {
                File::moveDirectory($directory, $repoPath . '/' . basename($directory));
            }

            return response()->json(['status' => 'error', 'message' => 'Error during update process: ' . $e->getMessage()], 500);
        }
    }

    public function rollback(Request $request, Instance $instance)
    {
        $repoPath = base_path('instances/' . $instance->id);
        $backupPaths = glob(base_path('instances/' . $instance->id . '/backup_*'), GLOB_ONLYDIR);
        
        if (empty($backupPaths)) {
            return response()->json(['status' => 'error', 'message' => 'No backup available for rollback.'], 500);
        }

        // Get the latest backup
        usort($backupPaths, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        $latestBackupPath = $backupPaths[0];

        try {
            // Clear current instance directory except for .env and venv
            foreach (File::files($repoPath) as $file) {
                if (basename($file) !== '.env') {
                    File::delete($file);
                }
            }

            foreach (File::directories($repoPath) as $directory) {
                if (basename($directory) !== 'venv') {
                    File::deleteDirectory($directory);
                }
            }

            // Restore from the latest backup
            foreach (File::files($latestBackupPath) as $file) {
                File::move($file, $repoPath . '/' . basename($file));
            }

            foreach (File::directories($latestBackupPath) as $directory) {
                File::moveDirectory($directory, $repoPath . '/' . basename($directory));
            }

            return response()->json(['status' => 'success', 'message' => 'Rollback completed successfully.']);
        } catch (\Exception $e) {
            Log::error('Error during rollback process for instance: ' . $instance->id, [
                'message' => $e->getMessage()
            ]);

            return response()->json(['status' => 'error', 'message' => 'Error during rollback process: ' . $e->getMessage()], 500);
        }
    }

    // 8. Helper Methods

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

    private function limitLines($filePath, $maxLines)
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
    
        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, count($lines) - $maxLines);
            file_put_contents($filePath, implode("\n", $lines) . "\n");
        }
    }

    private function storeLog(Instance $instance, $logEntry)
    {
        $logFile = base_path('instances/' . $instance->id . '/output.log');
        $archiveFolder = base_path($this->archiveFolder . $instance->id);
    
        if (!File::exists($archiveFolder)) {
            File::makeDirectory($archiveFolder, 0755, true);
        }
    
        $this->rotateLog($logFile, $archiveFolder);
    
        File::append($logFile, $logEntry . PHP_EOL);
    
        // Broadcast the log entry
        broadcast(new ConsoleOutputUpdated($instance->id, $logEntry));
    }

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
