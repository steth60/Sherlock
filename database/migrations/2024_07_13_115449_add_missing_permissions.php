<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;

class AddMissingPermissions extends Migration
{
    public function up()
    {
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

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }

    public function down()
    {
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

        foreach ($permissions as $permission) {
            Permission::where('name', $permission)->delete();
        }
    }
}
