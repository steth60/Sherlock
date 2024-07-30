<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class CacheDebugController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index()
    {
        $agentKeys = Cache::get('agent_keys', []);
        $groupKeys = Cache::get('group_keys', []);
        $departmentKeys = Cache::get('department_keys', []);
        $requesterKeys = Cache::get('requester_keys', []);

        $agents = array_map(fn($key) => Cache::get($key), $agentKeys);
        $groups = array_map(fn($key) => Cache::get($key), $groupKeys);
        $departments = array_map(fn($key) => Cache::get($key), $departmentKeys);
        $requesters = array_map(fn($key) => Cache::get($key), $requesterKeys);

        return view('debug-cache', compact('agents', 'groups', 'departments', 'requesters'));
    }
}
