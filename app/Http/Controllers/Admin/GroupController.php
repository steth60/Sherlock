<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Permission;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('permissions', 'childGroups')->orderBy('weight')->get();
        $permissions = Permission::all();
        return view('admin.groups.index', compact('groups', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:groups,name',
            'weight' => 'required|integer',
        ]);

        Group::create($request->only('name', 'weight'));

        return redirect()->route('admin.groups.index')->with('success', 'Group created successfully.');
    }

    public function assignPermissions(Request $request, Group $group)
    {
        $request->validate([
            'permissions' => 'required|array',
        ]);

        $group->permissions()->sync($request->input('permissions'));

        return redirect()->route('admin.groups.index')->with('success', 'Permissions assigned to group successfully.');
    }

    public function assignGroups(Request $request, Group $group)
    {
        $request->validate([
            'groups' => 'nullable|array',
        ]);

        $group->childGroups()->sync($request->input('groups', []));

        return redirect()->route('admin.groups.index')->with('success', 'Groups assigned successfully.');
    }

    public function rename(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string|unique:groups,name,' . $group->id,
        ]);

        $group->name = $request->input('name');
        $group->save();

        return redirect()->route('admin.groups.index')->with('success', 'Group renamed successfully.');
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return redirect()->route('admin.groups.index')->with('success', 'Group deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $weight => $id) {
            $group = Group::find($id);
            if ($group) {
                $group->weight = $weight;
                $group->save();
            }
        }
        return response()->json(['success' => true]);
    }
}
