<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $menuItems = MenuItem::with(['children' => function ($query) use ($user) {
            $query->orderBy('order');
        }])
        ->whereNull('parent_id')
        ->orderBy('order')
        ->get();

        $filteredMenuItems = $menuItems->filter(function ($menuItem) use ($user) {
            $hasParentPermission = empty($menuItem->permission) || $user->hasPermission($menuItem->permission);
            
            if ($menuItem->children->isEmpty()) {
                return $hasParentPermission;
            }

            $visibleChildren = $menuItem->children->filter(function ($child) use ($user) {
                return empty($child->permission) || $user->hasPermission($child->permission);
            });

            return $hasParentPermission || $visibleChildren->isNotEmpty();
        });

        return view('admin.nav.index', ['menuItems' => $filteredMenuItems]);
    }

    private function applyPermissionCheck($query, $user)
    {
        $query->where(function ($q) use ($user) {
            $q->whereNull('permission')
              ->orWhere('permission', '')
              ->orWhere(function ($r) use ($user) {
                  $r->whereNotNull('permission')
                    ->where('permission', '!=', '')
                    ->whereRaw('? = 1', [$user->hasPermission(DB::raw('permission'))]);
              });
        });
    }
    
    private function applyParentMenuCheck($query, $user)
    {
        $query->where(function ($q) use ($user) {
            $q->whereNull('parent_id')
              ->where(function ($r) use ($user) {
                  $r->whereNull('permission')
                    ->orWhere('permission', '')
                    ->orWhereHas('children', function ($subQuery) use ($user) {
                        $this->applyPermissionCheck($subQuery, $user);
                    });
              });
        })
        ->orWhere(function ($q) use ($user) {
            $this->applyPermissionCheck($q, $user);
        });
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required_if:is_dropdown,false|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer',
            'permission' => 'nullable|string|max:255',
            'is_dropdown' => 'boolean',
        ]);
    
        // Get the maximum order value
        $maxOrder = MenuItem::max('order') ?? 0;
    
        // Prepare the data
        $data = $request->all();
        if ($request->input('is_dropdown', false)) {
            $data['url'] = '#';
        }
    
        // Create the new menu item with the next order value
        $menuItem = new MenuItem($data);
        $menuItem->order = $maxOrder + 1;
        $menuItem->save();
    
        return response()->json(['success' => true]);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer',
            'permission' => 'nullable|string|max:255',
        ]);

        $menuItem->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->children->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete a menu item with children.'], 400);
        }
        $menuItem->delete();
        return response()->json(['success' => true]);
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $index => $itemId) {
            MenuItem::where('id', $itemId)->update(['order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }
}
