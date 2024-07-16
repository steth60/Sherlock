<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'View-dashboard',
            'View-Instance-page',
            'Create-Instance',
            'Stop-Instances',
            'Edit-Instances',
            'Start-Instance',
            'Delete-Instance',
            'Update-Instance',
            'View-Instance-files',
            'Edit-Instance-files',
            'View-Instance-notes',
            'Create-Instance-notes',
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
            'force_password_change',
            'View-service-desk-tools',
            'View-ticket-management',
            'View-task-project',
            'View-app-contents',
            'View-python-instances',
            'View-docker-instances',
            'View-vm-instances',
            'View-tasks',
            'View-tasks-dashboard',
            'View-tasks-task',
            'View-settings',
            'View-personal-information',
            'View-account-settings',
            'View-admin',
            'View-user-management',
            'View-group-management',
            'View-nav-menu',
            'View-maintain-mode',
            'View-update'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
