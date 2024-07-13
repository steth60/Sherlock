<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Log;

class LoadMenuItems
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $userPermissions = $user->groups->load('permissions')->pluck('permissions.*.name')->flatten()->unique();
            Log::info('User Permissions:', $userPermissions->toArray());

            $menuItems = MenuItem::whereNull('parent_id')
                ->orderBy('order')
                ->get()
                ->filter(function ($menuItem) use ($user) {
                    $hasPermission = $user->hasPermission($menuItem->permission);
                    Log::info('Checking permission for menu item', ['menuItem' => $menuItem->title, 'hasPermission' => $hasPermission]);
                    return $hasPermission;
                });

            foreach ($menuItems as $menuItem) {
                $menuItem->children = MenuItem::where('parent_id', $menuItem->id)
                    ->orderBy('order')
                    ->get()
                    ->filter(function ($subMenuItem) use ($user) {
                        $hasPermission = $user->hasPermission($subMenuItem->permission);
                        Log::info('Checking permission for sub-menu item', ['subMenuItem' => $subMenuItem->title, 'hasPermission' => $hasPermission]);
                        return $hasPermission;
                    });
            }

            Log::info('Menu items loaded', ['menuItems' => $menuItems]);
            view()->share('menuItems', $menuItems);
        }

        return $next($request);
    }
}
