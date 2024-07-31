<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use App\Models\Log;
use App\Events\UpdateProgress;

class LaraUpdater
{
    protected $versionFile;

    public function __construct()
    {
        $this->versionFile = base_path('version.txt');
    }

    public function installUpdate($options = [])
    {
        $this->broadcastProgress('Starting update...');
        $this->backupExistingFiles();
        $this->broadcastProgress('Backup completed.');
        
        $this->downloadUpdate();
        $this->broadcastProgress('Download completed.');
        
        $this->applyUpdate();
        $this->broadcastProgress('Update applied.');
        
        Artisan::call('migrate', ['--force' => true]);
        $this->broadcastProgress('Database migrated.');

        if (!empty($options['reseed'])) {
            Artisan::call('system:reseed');
            $this->broadcastProgress('Database reseeded.');
        }

        if (!empty($options['clear_cache'])) {
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            $this->broadcastProgress('Caches cleared.');
        }

        $latestVersion = $this->fetchLatestVersion();
        $this->setCurrentVersion($latestVersion);

        Log::create(['date' => now(), 'message' => 'Update installed successfully to version ' . $latestVersion]);
        $this->broadcastProgress('Update installed successfully.');
    }

    protected function broadcastProgress($message)
    {
        broadcast(new UpdateProgress($message));
    }

    public function getCurrentVersion()
    {
        return trim(File::get($this->versionFile));
    }

    public function setCurrentVersion($version)
    {
        File::put($this->versionFile, $version);
    }

    public function checkForUpdate()
    {
        $currentVersion = $this->getCurrentVersion();
        $latestVersion = $this->fetchLatestVersion();
        
        return version_compare($currentVersion, $latestVersion, '<');
    }

    public function fetchLatestVersion()
    {
        $repository = config('laraupdater.repository');
        $url = "https://api.github.com/repos/{$repository}/releases/latest";
        $token = config('laraupdater.token');

        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'laravel-updater',
            'Authorization' => 'token ' . $token,
        ])->get($url);

        if ($response->successful()) {
            return $response->json('tag_name');
        }

        \Log::error('GitHub API response', [
            'status' => $response->status(),
            'response' => $response->body()
        ]);

        throw new \Exception('Failed to fetch latest version from GitHub.');
    }

    protected function backupExistingFiles()
    {
        // Backup logic
        UpdateHelper::backup(['path/to/files']);
    }

    protected function downloadUpdate()
    {
        $repository = config('laraupdater.repository');
        $branch = config('laraupdater.branch');
        
        $url = "https://github.com/{$repository}/archive/refs/heads/{$branch}.zip";
        $response = Http::get($url);
        
        if ($response->successful()) {
            file_put_contents(storage_path('app/update.zip'), $response->body());
            \Log::info('Update downloaded successfully');
        } else {
            \Log::error('Failed to download update from GitHub', ['response' => $response->body()]);
            throw new \Exception('Failed to download update from GitHub.');
        }
    }

    protected function applyUpdate()
    {
        $zipFile = storage_path('app/update.zip');
        $extractPath = storage_path('app/update_temp'); // Temporary extraction path

        // Extract the ZIP file to a temporary location
        UpdateHelper::extract($zipFile, $extractPath);
        \Log::info('Update extracted successfully', ['extractPath' => $extractPath]);

        // Move the contents of the subfolder to the root directory
        $subfolderName = 'Sherlock-main'; // Name of the subfolder inside the ZIP
        $subfolderPath = $extractPath . '/' . $subfolderName;

        // Ensure the subfolder exists
        if (!is_dir($subfolderPath)) {
            \Log::error('Subfolder not found in the extracted contents', ['subfolderPath' => $subfolderPath]);
            throw new \Exception('Subfolder not found in the extracted contents.');
        }

        // Move contents from the subfolder to the root
        $this->moveDirectoryContents($subfolderPath, base_path());
        \Log::info('Update applied successfully');

        // Clean up temporary files
        File::deleteDirectory($extractPath);
        File::delete($zipFile);
    }

    /**
     * Move the contents of a directory to another directory.
     *
     * @param string $from
     * @param string $to
     * @return void
     */
    protected function moveDirectoryContents($from, $to)
    {
        $files = File::allFiles($from);
        $directories = File::directories($from);

        // Move all files
        foreach ($files as $file) {
            $destinationPath = $to . '/' . $file->getRelativePathname();
            File::move($file->getRealPath(), $destinationPath);
            \Log::info('Moved file', ['from' => $file->getRealPath(), 'to' => $destinationPath]);
        }

        // Move all directories
        foreach ($directories as $directory) {
            $destinationPath = $to . '/' . File::basename($directory);
            File::moveDirectory($directory, $destinationPath);
            \Log::info('Moved directory', ['from' => $directory, 'to' => $destinationPath]);
        }
    }
}
