<?php

namespace App\Http\Controllers\Instance;

use Illuminate\Http\Request;
use App\Models\Instance;

class DashboardController extends Controller
{
    public function index()
    {
        $totalInstances = Instance::count();
        $runningInstances = Instance::where('status', 'running')->count();
        $stoppedInstances = Instance::where('status', 'stopped')->count();
        $recentInstances = Instance::latest()->take(5)->get();

        return view('dashboard', compact('totalInstances', 'runningInstances', 'stoppedInstances', 'recentInstances'));
    }
}