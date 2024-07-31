<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        // First, let's truncate the permissions table and reset the ID counter
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        DB::statement('ALTER TABLE permissions AUTO_INCREMENT = 1;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $permissions = [
            // Dashboard
            'Dashboard' => null,
            'view_dashboard' => 'Dashboard',

            // Service Desk Tools
            'Service Desk Tools' => null,
            'view_service_desk_tools' => 'Service Desk Tools',
            'view_ticket_management' => 'Service Desk Tools',
            'manage_instances' => 'Service Desk Tools',

            // Instances
            'Instances' => null,
            'view_instance_page' => 'Instances',
            'create_instance' => 'Instances',
            'edit_instance' => 'Instances',
            'delete_instance' => 'Instances',
            'update_instance' => 'Instances',
            'start_instance' => 'Instances',
            'stop_instance' => 'Instances',
            'restart_instance' => 'Instances',
            'view_instance_files' => 'Instances',
            'edit_instance_files' => 'Instances',
            'view_instance_notes' => 'Instances',
            'create_instance_notes' => 'Instances',
            'view_instance_output' => 'Instances',
            'view_instance_status' => 'Instances',
            'view_instance_env' => 'Instances',
            'update_instance_env' => 'Instances',
            'view_instance_metrics' => 'Instances',
            'check_instance_updates' => 'Instances',
            'confirm_instance_updates' => 'Instances',

            // Projects
            'Projects' => null,
            'view_projects' => 'Projects',
            'create_project' => 'Projects',

            // Holidays
            'Holidays' => null,
            'view_holidays' => 'Holidays',
            'manage_holidays' => 'Holidays',

            // Knowledge Base
            'Knowledge Base' => null,
            'view_knowledge_base' => 'Knowledge Base',
            'manage_knowledge_base' => 'Knowledge Base',

            // Tools
            'Tools' => null,
            'view_tools' => 'Tools',
            'use_file_converter' => 'Tools',
            'use_code_formatter' => 'Tools',

            // User Settings
            'User Settings' => null,
            'manage_settings' => 'User Settings',
            'view_personal_information' => 'User Settings',
            'view_account_settings' => 'User Settings',
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
            'manage_nav_menu' => 'Admin',
            'manage_system_updates' => 'Admin',

            // System
            'System' => null,
            'view_menu' => 'System',
            'manage_schedules' => 'System',
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