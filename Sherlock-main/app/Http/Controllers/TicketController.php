<?php

namespace App\Http\Controllers;

use App\Models\UserFilter;
use App\Services\FreshserviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    protected $freshservice;

    public function __construct(FreshserviceService $freshservice)
    {
        $this->freshservice = $freshservice;
    }

    private function getSourceType($value)
    {
        $sourceTypes = [
            1 => 'Email',
            2 => 'Portal',
            3 => 'Phone',
            4 => 'Chat',
            5 => 'Feedback widget',
            6 => 'Yammer',
            7 => 'AWS Cloudwatch',
            8 => 'Pagerduty',
            9 => 'Walkup',
            10 => 'Slack',
        ];

        return $sourceTypes[$value] ?? 'Unknown';
    }

    private function getStatus($value)
    {
        $statuses = [
            2 => 'Open',
            3 => 'Pending',
            4 => 'Resolved',
            5 => 'Closed',
        ];

        return $statuses[$value] ?? 'Unknown';
    }

    private function getPriority($value)
    {
        $priorities = [
            1 => 'Low',
            2 => 'Medium',
            3 => 'High',
            4 => 'Urgent',
        ];

        return $priorities[$value] ?? 'Unknown';
    }

    public function index(Request $request)
    {
        $orderType = $request->input('order_type', 'desc');
        $page = $request->input('page', 1);

        $userFilter = UserFilter::where('user_id', Auth::id())->first();
        $filter = $userFilter ? $userFilter->filter : [];

        $query = $this->buildFilterQuery($filter);
        Log::info("Filter Query: $query");
        $tickets = $this->freshservice->filterTickets($orderType, $page, $query);

        $departments = $this->freshservice->getAllDepartments();
        Log::info("Departments: " . json_encode($departments));
        $groups = $this->freshservice->getAllGroups();
        Log::info("Groups: " . json_encode($groups));
        $agents = $this->freshservice->getAllAgents();
        Log::info("Agents: " . json_encode($agents));
        $requesters = $this->getRequestersFromTickets($tickets['tickets'] ?? []);

        if (isset($tickets['tickets']) && is_array($tickets['tickets'])) {
            foreach ($tickets['tickets'] as &$ticket) {
                $ticket['department'] = $this->getDepartmentName($ticket['department_id'] ?? null, $departments);
                $ticket['requester'] = $this->getRequesterName($ticket['requester_id'] ?? null, $requesters);
                $ticket['group'] = $this->getGroupName($ticket['group_id'] ?? null, $groups);
                $ticket['responder'] = $this->getAgentName($ticket['responder_id'] ?? null, $agents);
                $ticket['source'] = $this->getSourceType($ticket['source'] ?? null);
                $ticket['status'] = $this->getStatus($ticket['status'] ?? null);
                $ticket['priority'] = $this->getPriority($ticket['priority'] ?? null);
                Log::info("Processed Ticket: " . json_encode($ticket));
            }
        } else {
            $tickets['tickets'] = [];
        }

        return view('tickets.index', compact('tickets', 'orderType', 'page', 'filter', 'query', 'departments', 'groups', 'agents'));
    }

    public function loadMore(Request $request)
    {
        $orderType = $request->input('order_type', 'desc');
        $page = $request->input('page', 1);

        $userFilter = UserFilter::where('user_id', Auth::id())->first();
        $filter = $userFilter ? $userFilter->filter : [];

        $query = $this->buildFilterQuery($filter);
        
        Log::info("Load More Filter Query: $query");
        
        $tickets = $this->freshservice->filterTickets($orderType, $page, $query);

        $departments = $this->freshservice->getAllDepartments();
        Log::info("Departments: " . json_encode($departments));
        $groups = $this->freshservice->getAllGroups();
        Log::info("Groups: " . json_encode($groups));
        $agents = $this->freshservice->getAllAgents();
        Log::info("Agents: " . json_encode($agents));
        $requesters = $this->getRequestersFromTickets($tickets['tickets'] ?? []);
        
        if (isset($tickets['tickets']) && is_array($tickets['tickets'])) {
            foreach ($tickets['tickets'] as &$ticket) {
                $ticket['department'] = $this->getDepartmentName($ticket['department_id'] ?? null, $departments);
                $ticket['requester'] = $this->getRequesterName($ticket['requester_id'] ?? null, $requesters);
                $ticket['group'] = $this->getGroupName($ticket['group_id'] ?? null, $groups);
                $ticket['responder'] = $this->getAgentName($ticket['responder_id'] ?? null, $agents);
                $ticket['source'] = $this->getSourceType($ticket['source'] ?? null);
                $ticket['status'] = $this->getStatus($ticket['status'] ?? null);
                $ticket['priority'] = $this->getPriority($ticket['priority'] ?? null);
                Log::info("Processed Ticket: " . json_encode($ticket));
            }
        } else {
            $tickets['tickets'] = [];
        }

        return view('tickets.partials.ticket-list', compact('tickets'));
    }

    private function getRequestersFromTickets($tickets)
    {
        if (!is_array($tickets)) {
            return [];
        }

        $requesterIds = array_unique(array_column($tickets, 'requester_id'));
        $requesters = [];
        foreach ($requesterIds as $id) {
            if ($id !== null) {
                $requesters[$id] = $this->freshservice->getRequester($id);
            }
        }
        Log::info("Requesters: " . json_encode($requesters));
        return $requesters;
    }

    private function getDepartmentName($id, $departments)
    {
        if ($id === null || !is_array($departments)) {
            return 'N/A';
        }

        foreach ($departments as $department) {
            if ((is_array($department) && $department['id'] == $id) || (is_object($department) && $department->id == $id)) {
                return $department['name'] ?? 'N/A';
            }
        }
        return 'N/A';
    }

    private function getGroupName($id, $groups)
    {
        if ($id === null || !is_array($groups)) {
            return 'N/A';
        }

        foreach ($groups as $group) {
            if ((is_array($group) && $group['id'] == $id) || (is_object($group) && $group->id == $id)) {
                return $group['name'] ?? 'N/A';
            }
        }
        return 'N/A';
    }

    private function getAgentName($id, $agents)
    {
        if ($id === null || !is_array($agents)) {
            return 'N/A';
        }

        foreach ($agents as $agent) {
            if ((is_array($agent) && $agent['id'] == $id) || (is_object($agent) && $agent->id == $id)) {
                return ($agent['first_name'] ?? '') . ' ' . ($agent['last_name'] ?? '');
            }
        }
        return 'N/A';
    }

    private function getRequesterName($id, $requesters)
    {
        if ($id === null || !is_array($requesters)) {
            return 'N/A';
        }

        if (isset($requesters[$id])) {
            $requester = $requesters[$id];
            return ($requester['first_name'] ?? '') . ' ' . ($requester['last_name'] ?? '');
        }
        return 'N/A';
    }

    private function buildFilterQuery($filter)
    {
        $queryParts = [];
        foreach ($filter as $key => $value) {
            if (!empty($value)) {
                if ($key === 'time_frame') {
                    $dateRange = $this->getDateRangeForTimeFrame($value, $filter['start_date'] ?? null, $filter['end_date'] ?? null);
                    if ($dateRange) {
                        $queryParts[] = $dateRange;
                    }
                } elseif (in_array($key, ['priority', 'status', 'department_id', 'group_id', 'agent_id']) && is_array($value)) {
                    $subParts = array_map(fn($item) => "$key:$item", $value);
                    if (!empty($subParts)) {
                        $queryParts[] = '(' . implode(' OR ', $subParts) . ')';
                    }
                } elseif (is_numeric($value)) {
                    $queryParts[] = "$key:$value";
                } elseif (is_bool($value)) {
                    $queryParts[] = "$key:" . ($value ? 'true' : 'false');
                } elseif (is_string($value) && !in_array($key, ['start_date', 'end_date'])) {
                    $queryParts[] = "$key:'$value'";
                }
            }
        }
        return implode(' AND ', $queryParts);
    }

    private function getDateRangeForTimeFrame($timeFrame, $startDate = null, $endDate = null)
    {
        $now = now();
        switch ($timeFrame) {
            case 'today':
                return "created_at:>'{$now->startOfDay()->format('Y-m-d')}' AND created_at:<'{$now->endOfDay()->format('Y-m-d')}'";
            case 'yesterday':
                $yesterday = $now->subDay();
                return "created_at:>'{$yesterday->startOfDay()->format('Y-m-d')}' AND created_at:<'{$yesterday->endOfDay()->format('Y-m-d')}'";
            case 'this_week':
                $startOfWeek = $now->startOfWeek();
                return "created_at:>'{$startOfWeek->format('Y-m-d')}' AND created_at:<'{$now->format('Y-m-d')}'";
            case 'last_week':
                $startOfLastWeek = $now->subWeek()->startOfWeek();
                $endOfLastWeek = $startOfLastWeek->copy()->endOfWeek();
                return "created_at:>'{$startOfLastWeek->format('Y-m-d')}' AND created_at:<'{$endOfLastWeek->format('Y-m-d')}'";
            case 'this_month':
                $startOfMonth = $now->startOfMonth();
                return "created_at:>'{$startOfMonth->format('Y-m-d')}' AND created_at:<'{$now->format('Y-m-d')}'";
            case 'last_month':
                $startOfLastMonth = $now->subMonth()->startOfMonth();
                $endOfLastMonth = $startOfLastMonth->copy()->endOfMonth();
                return "created_at:>'{$startOfLastMonth->format('Y-m-d')}' AND created_at:<'{$endOfLastMonth->format('Y-m-d')}'";
            case 'last_3_months':
                $threeMonthsAgo = $now->copy()->subMonths(3)->startOfDay();
                return "created_at:>'{$threeMonthsAgo->format('Y-m-d')}' AND created_at:<'{$now->format('Y-m-d')}'";
            case 'last_6_months':
                $sixMonthsAgo = $now->copy()->subMonths(6)->startOfDay();
                return "created_at:>'{$sixMonthsAgo->format('Y-m-d')}' AND created_at:<'{$now->format('Y-m-d')}'";
            case 'custom':
                if ($startDate && $endDate) {
                    return "created_at:>'{$startDate}' AND created_at:<'{$endDate}'";
                }
                break;
        }
        return null;
    }

    public function refreshCache()
    {
        // Cache all departments
        $departments = $this->freshservice->getAllDepartments();
        Log::info('Cached departments: ' . json_encode($departments));
    
        // Cache all groups
        $groups = $this->freshservice->getAllGroups();
        Log::info('Cached groups: ' . json_encode($groups));
    
        // Cache all agents
        $agents = $this->freshservice->getAllAgents();
        Log::info('Cached agents: ' . json_encode($agents));
    }
    public function saveFilter(Request $request)
    {
        $filter = $request->input('filter', []);

        UserFilter::updateOrCreate(
            ['user_id' => Auth::id()],
            ['filter' => $filter]
        );

        return redirect()->route('tickets.index');
    }
}
