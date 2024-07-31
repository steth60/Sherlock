<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function remember($key, $callback, $ttl = 1440) // 1440 minutes = 24 hours
    {
        $this->addKeyToCacheList($key);
        return Cache::remember($key, $ttl * 60, $callback); // TTL is in seconds
    }

    public function get($key)
    {
        return Cache::get($key);
    }

    public function put($key, $value, $ttl = 1440)
    {
        $this->addKeyToCacheList($key);
        Cache::put($key, $value, $ttl * 60); // TTL is in seconds
    }

    public function forget($key)
    {
        $this->removeKeyFromCacheList($key);
        Cache::forget($key);
    }

    private function addKeyToCacheList($key)
    {
        $listKey = $this->getListKey($key);
        $keys = Cache::get($listKey, []);
        if (!in_array($key, $keys)) {
            $keys[] = $key;
            Cache::put($listKey, $keys, 1440 * 60); // TTL for list keys
        }
    }

    private function removeKeyFromCacheList($key)
    {
        $listKey = $this->getListKey($key);
        $keys = Cache::get($listKey, []);
        if (($index = array_search($key, $keys)) !== false) {
            unset($keys[$index]);
            Cache::put($listKey, $keys, 1440 * 60); // TTL for list keys
        }
    }

    private function getListKey($key)
    {
        if (strpos($key, 'agent_') !== false) {
            return 'agent_keys';
        } elseif (strpos($key, 'group_') !== false) {
            return 'group_keys';
        } elseif (strpos($key, 'department_') !== false) {
            return 'department_keys';
        } elseif (strpos($key, 'requester_') !== false) {
            return 'requester_keys';
        }
        return 'other_keys';
    }
}
