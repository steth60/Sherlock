<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Permission;

class AssignPermissionsToAdminGroupSeeder extends Seeder
{
    public function run()
    {
        $adminGroup = Group::find(1);
        $permissions = [
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
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            $adminGroup->permissions()->attach($permission);
        }
    }
}
