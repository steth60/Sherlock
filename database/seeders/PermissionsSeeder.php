<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Dashboard
            'Dashboard' => null,
            'View-dashboard' => 'Dashboard',

            // Instances
            'Instances' => null,
            'View-Instance-page' => 'Instances',
            'Create-Instance' => 'Instances',
            'Edit-Instances' => 'Instances',
            'Delete-Instance' => 'Instances',
            'Update-Instance' => 'Instances',
            'Start-Instance' => 'Instances',
            'Stop-Instances' => 'Instances',
            'restart_instance' => 'Instances',
            'View-Instance-files' => 'Instances',
            'Edit-Instance-files' => 'Instances',
            'View-Instance-notes' => 'Instances',
            'Create-Instance-notes' => 'Instances',
            'view_instance_output' => 'Instances',
            'view_instance_status' => 'Instances',
            'view_instance_env' => 'Instances',
            'update_instance_env' => 'Instances',
            'view_instance_metrics' => 'Instances',
            'check_instance_updates' => 'Instances',
            'confirm_instance_updates' => 'Instances',

            // App Contents
            'App Contents' => null,
            'View-app-contents' => 'App Contents',
            'View-python-instances' => 'App Contents',
            'View-docker-instances' => 'App Contents',
            'View-vm-instances' => 'App Contents',

            // Tasks
            'Tasks' => null,
            'View-tasks' => 'Tasks',
            'View-tasks-dashboard' => 'Tasks',
            'View-tasks-task' => 'Tasks',

            // Service Desk Tools
            'Service Desk Tools' => null,
            'View-service-desk-tools' => 'Service Desk Tools',
            'View-ticket-management' => 'Service Desk Tools',
            'View-task-project' => 'Service Desk Tools',

            // User Settings
            'User Settings' => null,
            'View-settings' => 'User Settings',
            'View-personal-information' => 'User Settings',
            'View-account-settings' => 'User Settings',
            'manage_personal_info' => 'User Settings',
            'change_email' => 'User Settings',
            'change_password' => 'User Settings',
            'manage_mfa' => 'User Settings',
            'manage_account_recovery' => 'User Settings',
            'manage_trusted_devices' => 'User Settings',

            // Admin
            'Admin' => null,
            'admin_access' => 'Admin',
            'view_admin_dashboard' => 'Admin',
            'manage_permissions' => 'Admin',
            'manage_groups' => 'Admin',
            'manage_users' => 'Admin',
            'view_unverified_email_users' => 'Admin',
            'view_mfa_not_enabled_users' => 'Admin',
            'force_password_change' => 'Admin',

            // Admin Tools
            'Admin Tools' => null,
            'View-admin' => 'Admin Tools',
            'View-user-management' => 'Admin Tools',
            'View-group-management' => 'Admin Tools',
            'View-nav-menu' => 'Admin Tools',
            'View-maintain-mode' => 'Admin Tools',
            'View-update' => 'Admin Tools',

            // System
            'System' => null,
            'view_menu' => 'System',
            'manage_schedules' => 'System',
            'manage_settings' => 'System',
            'manage_nav_menu' => 'System',
        ];

        $permissionIds = [];

        foreach ($permissions as $permission => $parent) {
            $created = Permission::create(['name' => $permission]);
            $permissionIds[$permission] = $created->id;
        }

        // Set parent IDs
        foreach ($permissions as $permission => $parent) {
            if ($parent !== null) {
                Permission::where('name', $permission)->update(['parent_id' => $permissionIds[$parent]]);
            }
        }
    }
}