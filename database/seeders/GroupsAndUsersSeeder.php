<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GroupsAndUsersSeeder extends Seeder
{
    public function run()
    {
        // Create Groups
        $adminGroup = Group::firstOrCreate(['name' => 'Admin'], ['weight' => 1]);
        $editorGroup = Group::firstOrCreate(['name' => 'Editor'], ['weight' => 2]);
        $viewerGroup = Group::firstOrCreate(['name' => 'Viewer'], ['weight' => 3]);

        // Log permissions for debugging
        $permissions = Permission::all();
        Log::info('All permissions:', $permissions->toArray());

        // Assign Permissions to Groups
        $adminPermissions = Permission::all();
        $editorPermissions = Permission::whereIn('name', [
            'View-dashboard',
            'View-Instance-page',
            'Create-Instance',
            'Stop-Instances',
            'Edit-Instances',
            'Start-Instance',
            'Update-Instance',
            'View-Instance-files',
            'Edit-Instance-files',
            'View-Instance-notes',
            'Create-Instance-notes',
        ])->get();

        $viewerPermissions = Permission::whereIn('name', [
            'View-dashboard',
            'View-Instance-page',
            'View-Instance-files',
            'View-Instance-notes',
        ])->get();

        // Log permissions for debugging
        Log::info('Admin permissions:', $adminPermissions->toArray());
        Log::info('Editor permissions:', $editorPermissions->toArray());
        Log::info('Viewer permissions:', $viewerPermissions->toArray());

        $adminGroup->permissions()->syncWithoutDetaching($adminPermissions);
        $editorGroup->permissions()->syncWithoutDetaching($editorPermissions);
        $viewerGroup->permissions()->syncWithoutDetaching($viewerPermissions);

        // Create Users
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password')]
        );

        $editorUser = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            ['name' => 'Editor User', 'password' => Hash::make('password')]
        );

        $viewerUser = User::firstOrCreate(
            ['email' => 'viewer@example.com'],
            ['name' => 'Viewer User', 'password' => Hash::make('password')]
        );

        // Assign Groups to Users
        $adminUser->groups()->syncWithoutDetaching($adminGroup);
        $editorUser->groups()->syncWithoutDetaching($editorGroup);
        $viewerUser->groups()->syncWithoutDetaching($viewerGroup);
    }
}
