<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $rootPermissions = Permission::whereNull('parent_id')->with('children')->get();
        $allPermissions = Permission::all();
        return view('admin.permissions.index', compact('rootPermissions', 'allPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'parent_id' => 'nullable|exists:permissions,id',
        ]);

        Permission::create($request->only(['name', 'parent_id']));

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function rename(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
            'parent_id' => 'nullable|exists:permissions,id',
        ]);

        $permission->name = $request->input('name');
        $permission->parent_id = $request->input('parent_id');
        $permission->save();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }
    public function getChildren(Permission $permission)
{
    return $permission->children;
}

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
