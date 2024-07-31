<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FreshserviceService
{
    protected $apiKey;
    protected $domain;
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->apiKey = env('FRESHSERVICE_API_KEY');
        $this->domain = env('FRESHSERVICE_DOMAIN');
        $this->cacheService = $cacheService;
    }

    public function filterTickets($orderType = 'desc', $page = 1, $query = '', $perPage = 30)
    {
        $baseUrl = "https://{$this->domain}.freshservice.com/api/v2/tickets/filter";
    
        $params = [
            'order_type' => $orderType,
            'page' => $page,
            'per_page' => $perPage,
        ];
    
        if (!empty($query)) {
            $params['query'] = '"' . $query . '"';
        }
    
        $url = $baseUrl . '?' . http_build_query($params);
        
        Log::info("Querying Freshservice API: $url");
    
        $response = Http::withBasicAuth($this->apiKey, 'X')->get($url);
    
        if ($response->failed()) {
            Log::error("Failed to query Freshservice API: " . $response->body());
            return ['tickets' => []];
        }
    
        return $response->json();
    }

    public function getAgent($id)
{
    return $this->cacheService->remember("agent_{$id}", function () use ($id) {
        $response = Http::withBasicAuth($this->apiKey, 'X')
            ->get("https://{$this->domain}.freshservice.com/api/v2/agents/{$id}");

        if ($response->failed()) {
            Log::error("Failed to get agent with ID {$id}: " . $response->body());
            return null;
        }

        $data = $response->json();
        Log::info("Agent data for ID {$id}: " . json_encode($data));
        return $data['agent'] ?? null;
    });
}


    public function getRequester($id)
    {
        return $this->cacheService->remember("requester_{$id}", function () use ($id) {
            $response = Http::withBasicAuth($this->apiKey, 'X')
                ->get("https://{$this->domain}.freshservice.com/api/v2/requesters/{$id}");

            if ($response->failed()) {
                Log::error("Failed to get requester with ID {$id}: " . $response->body());
                return null;
            }

            $data = $response->json();
            Log::info("Requester data for ID {$id}: " . json_encode($data));
            return $data['requester'] ?? null;
        });
    }

    public function getGroup($id)
    {
        return $this->cacheService->remember("group_{$id}", function () use ($id) {
            $response = Http::withBasicAuth($this->apiKey, 'X')
                ->get("https://{$this->domain}.freshservice.com/api/v2/groups/{$id}");

            if ($response->failed()) {
                Log::error("Failed to get group with ID {$id}: " . $response->body());
                return null;
            }

            $data = $response->json();
            Log::info("Group data for ID {$id}: " . json_encode($data));
            return $data['group'] ?? null;
        });
    }

    public function getDepartment($id)
    {
        return $this->cacheService->remember("department_{$id}", function () use ($id) {
            $response = Http::withBasicAuth($this->apiKey, 'X')
                ->get("https://{$this->domain}.freshservice.com/api/v2/departments/{$id}");

            if ($response->failed()) {
                Log::error("Failed to get department with ID {$id}: " . $response->body());
                return null;
            }

            $data = $response->json();
            Log::info("Department data for ID {$id}: " . json_encode($data));
            return $data['department'] ?? null;
        });
    }

    public function getAllDepartments()
    {
        return $this->cacheService->remember("all_departments", function () {
            $response = Http::withBasicAuth($this->apiKey, 'X')
                ->get("https://{$this->domain}.freshservice.com/api/v2/departments");

            if ($response->failed()) {
                Log::error("Failed to get all departments: " . $response->body());
                return [];
            }

            $data = $response->json();
            Log::info("All departments data: " . json_encode($data));
            return $data['departments'] ?? [];
        });
    }

    public function getAllGroups()
    {
        return $this->cacheService->remember("all_groups", function () {
            $response = Http::withBasicAuth($this->apiKey, 'X')
                ->get("https://{$this->domain}.freshservice.com/api/v2/groups");

            if ($response->failed()) {
                Log::error("Failed to get all groups: " . $response->body());
                return [];
            }

            $data = $response->json();
            Log::info("All groups data: " . json_encode($data));
            return $data['groups'] ?? [];
        });
    }

    public function getAllAgents()
    {
        return $this->cacheService->remember("all_agents", function () {
            $response = Http::withBasicAuth($this->apiKey, 'X')
                ->get("https://{$this->domain}.freshservice.com/api/v2/agents");

            if ($response->failed()) {
                Log::error("Failed to get all agents: " . $response->body());
                return [];
            }

            $data = $response->json();
            Log::info("All agents data: " . json_encode($data));
            return $data['agents'] ?? [];
        });
    }
}
