<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Permission;

class GroupsSeeder extends Seeder
{
    public function run()
    {
        // Create Groups
        $superAdminGroup = Group::firstOrCreate(['name' => 'Super Admin'], ['weight' => 0]);
        $adminGroup = Group::firstOrCreate(['name' => 'Admin'], ['weight' => 1]);
        $editorGroup = Group::firstOrCreate(['name' => 'Editor'], ['weight' => 3]);
        $viewerGroup = Group::firstOrCreate(['name' => 'Viewer'], ['weight' => 2]);

        // Assign all permissions to Super Admin Group
        $allPermissions = Permission::all();
        $superAdminGroup->permissions()->sync($allPermissions);

        // Define specific permissions for other groups
        $editorPermissions = Permission::whereIn('name', [
            'view_menu',
            'restart_instance',
            'view_instance_output',
            'check_instance_updates',
            'confirm_instance_updates',
            'view_instance_status',
            'view_instance_env',
            'update_instance_env',
            'view_instance_metrics',
            'manage_schedules',
            'manage_settings',
            'manage_personal_info',
            'change_email',
            'change_password',
            'manage_mfa',
            'manage_account_recovery',
            'manage_trusted_devices',
            'admin_access',
            'view_admin_dashboard',
            'view_unverified_email_users',
            'view_mfa_not_enabled_users',
            'manage_permissions',
            'manage_groups',
            'manage_users',
            'manage_nav_menu',
            'force_password_change'
        ])->get();

        $viewerPermissions = Permission::whereIn('name', [
            'view_menu',
            'view_instance_output',
            'view_instance_status',
            'view_instance_metrics',
        ])->get();

        // Assign permissions to Admin, Editor and Viewer Groups
        $adminGroup->permissions()->sync($allPermissions);
        $editorGroup->permissions()->sync($editorPermissions);
        $viewerGroup->permissions()->sync($viewerPermissions);
    }
}
