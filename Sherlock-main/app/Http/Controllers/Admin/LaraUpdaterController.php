<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Helpers\LaraUpdater;
use Illuminate\Support\Facades\Log;

class LaraUpdaterController extends Controller
{
    protected $updater;

    public function __construct(LaraUpdater $updater)
    {
        $this->updater = $updater;
    }

    public function index()
    {
        $currentVersion = $this->updater->getCurrentVersion();
        $updateAvailable = $this->updater->checkForUpdate();
        $latestVersion = $updateAvailable ? $this->updater->fetchLatestVersion() : null;
        
        return view('admin.laraupdater.index', compact('currentVersion', 'updateAvailable', 'latestVersion'));
    }

    public function check()
    {
        $updateAvailable = $this->updater->checkForUpdate();
        return redirect()->back()->with('updateAvailable', $updateAvailable);
    }

    public function install()
    {
        try {
            $this->updater->installUpdate();
            return redirect()->back()->with('status', 'Update installed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function logs()
    {
        $logs = Log::all(); // Ensure you have a method to fetch logs
        return view('admin.laraupdater.logs', compact('logs'));
    }
}
