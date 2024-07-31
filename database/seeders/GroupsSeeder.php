<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class GroupsSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks and truncate tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Group::truncate();
        DB::table('group_permission')->truncate(); // Assuming this is your pivot table name
        DB::statement('ALTER TABLE groups AUTO_INCREMENT = 1;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Groups
        $superAdminGroup = Group::create(['name' => 'Super Admin', 'weight' => 0]);
        $adminGroup = Group::create(['name' => 'Admin', 'weight' => 1]);
        $editorGroup = Group::create(['name' => 'Editor', 'weight' => 2]);
        $viewerGroup = Group::create(['name' => 'Viewer', 'weight' => 3]);

        // Assign all permissions to Super Admin Group
        $allPermissions = Permission::all();
        $superAdminGroup->permissions()->sync($allPermissions);

        // Define specific permissions for other groups
        $editorPermissions = Permission::whereIn('name', [
            'view_menu',
            'view_dashboard',
            'view_service_desk_tools',
            'view_ticket_management',
            'manage_instances',
            'view_instance_page',
            'view_instance_output',
            'view_instance_status',
            'view_instance_env',
            'view_instance_metrics',
            'check_instance_updates',
            'view_projects',
            'view_holidays',
            'view_knowledge_base',
            'view_tools',
            'use_file_converter',
            'use_code_formatter',
            'manage_settings',
            'view_personal_information',
            'view_account_settings',
            'manage_personal_info',
            'change_email',
            'change_password',
            'manage_mfa',
            'manage_account_recovery',
            'manage_trusted_devices',
            'manage_schedules'
        ])->get();

        $viewerPermissions = Permission::whereIn('name', [
            'view_menu',
            'view_dashboard',
            'view_service_desk_tools',
            'view_instance_page',
            'view_instance_output',
            'view_instance_status',
            'view_instance_metrics',
            'view_projects',
            'view_holidays',
            'view_knowledge_base',
            'view_tools',
            'view_personal_information',
            'view_account_settings'
        ])->get();

        // Assign permissions to Admin, Editor and Viewer Groups
        $adminGroup->permissions()->sync($allPermissions);
        $editorGroup->permissions()->sync($editorPermissions);
        $viewerGroup->permissions()->sync($viewerPermissions);
    }
}