<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;

class MenuController extends Controller
{
  
    public function index()
{
    $menuItems = MenuItem::with('children')
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->get();
    return view('admin.nav.index', compact('menuItems'));
}

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer',
            'permission' => 'nullable|string|max:255',
        ]);

        MenuItem::create($request->all());
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
        $menuOrder = $request->input('order');
        foreach ($menuOrder as $index => $id) {
            $menuItem = MenuItem::find($id);
            $menuItem->order = $index + 1;
            $menuItem->save();
        }
        return response()->json(['success' => true]);
    }
}
