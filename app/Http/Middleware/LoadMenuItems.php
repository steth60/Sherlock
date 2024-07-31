<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\MenuItem;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class LoadMenuItems
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            $menuItems = MenuItem::with(['children' => function ($query) {
                $query->orderBy('order');
            }])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

            $filteredMenuItems = $menuItems->filter(function ($menuItem) use ($user) {
                // Check if the user has permission for the parent item
                $hasParentPermission = empty($menuItem->permission) || $user->hasPermission($menuItem->permission);
                
                // If it's a single button (has permission and no children), only show if user has permission
                if ($menuItem->children->isEmpty() && !empty($menuItem->permission)) {
                    return $hasParentPermission;
                }
                
                // Filter children based on permissions
                $visibleChildren = $menuItem->children->filter(function ($child) use ($user) {
                    return empty($child->permission) || $user->hasPermission($child->permission);
                });

                // For items with children, keep if:
                // 1. User has permission for the parent item and it has visible children
                // 2. User doesn't have permission for the parent item but it has visible children
                return ($hasParentPermission && $visibleChildren->isNotEmpty()) ||
                       (!$hasParentPermission && $visibleChildren->isNotEmpty());
            });

            // Log the filtered menu items for debugging
            Log::info('Filtered Menu Items: ' . json_encode($filteredMenuItems->pluck('title')));

            View::share('menuItems', $filteredMenuItems);
        } else {
            View::share('menuItems', collect([]));
        }

        return $next($request);
    }
}