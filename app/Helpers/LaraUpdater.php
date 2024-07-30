<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use App\Models\Log;

class LaraUpdater
{
    public function installUpdate()
    {
        $this->backupExistingFiles();
        $this->downloadUpdate();
        $this->applyUpdate();
        Artisan::call('migrate', ['--force' => true]);

        // Log the update
        Log::create(['date' => now(), 'message' => 'Update installed successfully.']);
    }

    public function getCurrentVersion()
    {
        return config('app.version');
    }

    public function checkForUpdate()
    {
        $currentVersion = $this->getCurrentVersion();
        $latestVersion = $this->fetchLatestVersion();
        
        return version_compare($currentVersion, $latestVersion, '<');
    }

    public function fetchLatestVersion() // Change visibility to public
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
        } else {
            throw new \Exception('Failed to download update from GitHub.');
        }
    }

    protected function applyUpdate()
    {
        // Extract and apply update logic
        $zipFile = storage_path('app/update.zip');
        $extractPath = base_path();
        
        UpdateHelper::extract($zipFile, $extractPath);
    }
}
