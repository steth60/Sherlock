<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

class MenuItemsSeeder extends Seeder
{
    public function run()
    {
        // Reset the table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MenuItem::truncate();
        DB::statement('ALTER TABLE menu_items AUTO_INCREMENT = 1;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Dashboard
        MenuItem::create([
            'title' => 'Dashboard',
            'url' => 'route:home',
            'icon' => 'mdi mdi-view-dashboard',
            'order' => 1,
            'permission' => 'view_dashboard'
        ]);

        // Service Desk Tools
        $serviceDeskTools = MenuItem::create([
            'title' => 'Service Desk Tools',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-tools',
            'order' => 2,
        ]);

        MenuItem::create([
            'title' => 'Ticket Management',
            'url' => 'route:tickets.index',
            'parent_id' => $serviceDeskTools->id,
            'order' => 1,
            'permission' => 'view_ticket_management'
        ]);

        MenuItem::create([
            'title' => 'Instances',
            'url' => 'route:instances.index',
            'parent_id' => $serviceDeskTools->id,
            'order' => 2,
            'permission' => 'manage_instances'
        ]);

        // Projects
        $projects = MenuItem::create([
            'title' => 'Projects',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-briefcase-outline',
            'order' => 3,
        ]);

        MenuItem::create([
            'title' => 'All Projects',
            'url' => 'route:projects.index',
            'parent_id' => $projects->id,
            'order' => 1,
            'permission' => 'view_projects'
        ]);

        MenuItem::create([
            'title' => 'Create Project',
            'url' => 'route:projects.create',
            'parent_id' => $projects->id,
            'order' => 2,
            'permission' => 'create_project'
        ]);

        // Holidays
        $holidays = MenuItem::create([
            'title' => 'Holidays',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-calendar-star',
            'order' => 4,
        ]);

        MenuItem::create([
            'title' => 'Holiday Calendar',
            'url' => 'route:holiday.calendar',
            'parent_id' => $holidays->id,
            'order' => 1,
            'permission' => 'view_holidays'
        ]);

        MenuItem::create([
            'title' => 'Manage Holidays',
            'url' => 'route:holiday.index',
            'parent_id' => $holidays->id,
            'order' => 2,
            'permission' => 'manage_holidays'
        ]);

        // Knowledge Base
        $knowledgeBase = MenuItem::create([
            'title' => 'Knowledge Base',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-book-open-page-variant',
            'order' => 5,
        ]);

        MenuItem::create([
            'title' => 'Articles',
            'url' => 'route:kb.articles.index',
            'parent_id' => $knowledgeBase->id,
            'order' => 1,
            'permission' => 'view_knowledge_base'
        ]);

        MenuItem::create([
            'title' => 'Categories',
            'url' => 'route:kb.categories.index',
            'parent_id' => $knowledgeBase->id,
            'order' => 2,
            'permission' => 'manage_knowledge_base'
        ]);

        // Tools
        $tools = MenuItem::create([
            'title' => 'Tools',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-wrench',
            'order' => 6,
        ]);

        MenuItem::create([
            'title' => 'File Converter',
            'url' => 'route:tools.file-converter',
            'parent_id' => $tools->id,
            'order' => 1,
            'permission' => 'use_file_converter'
        ]);

        MenuItem::create([
            'title' => 'Code Formatter',
            'url' => 'route:tools.code-formatter',
            'parent_id' => $tools->id,
            'order' => 2,
            'permission' => 'use_code_formatter'
        ]);

        // Settings
        $settings = MenuItem::create([
            'title' => 'Settings',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-settings',
            'order' => 7,
        ]);

        MenuItem::create([
            'title' => 'Personal Information',
            'url' => 'route:settings.personal-info',
            'parent_id' => $settings->id,
            'order' => 1,
            'permission' => 'view_personal_information'
        ]);

        MenuItem::create([
            'title' => 'Account Settings',
            'url' => 'route:settings.account-info',
            'parent_id' => $settings->id,
            'order' => 2,
            'permission' => 'view_account_settings'
        ]);

        MenuItem::create([
            'title' => 'Security',
            'url' => 'route:settings.mfa',
            'parent_id' => $settings->id,
            'order' => 3,
            'permission' => 'manage_mfa'
        ]);

        // Admin
        $admin = MenuItem::create([
            'title' => 'Admin',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-shield-account',
            'order' => 8,
        ]);

        MenuItem::create([
            'title' => 'Dashboard',
            'url' => 'route:admin.dashboard',
            'parent_id' => $admin->id,
            'order' => 1,
            'permission' => 'view_admin_dashboard'
        ]);

        MenuItem::create([
            'title' => 'User Management',
            'url' => 'route:admin.users.index',
            'parent_id' => $admin->id,
            'order' => 2,
            'permission' => 'manage_users'
        ]);

        MenuItem::create([
            'title' => 'Group Management',
            'url' => 'route:admin.groups.index',
            'parent_id' => $admin->id,
            'order' => 3,
            'permission' => 'manage_groups'
        ]);

        MenuItem::create([
            'title' => 'Permissions',
            'url' => 'route:admin.permissions.index',
            'parent_id' => $admin->id,
            'order' => 4,
            'permission' => 'manage_permissions'
        ]);

        MenuItem::create([
            'title' => 'Nav Menu',
            'url' => 'route:admin.nav.index',
            'parent_id' => $admin->id,
            'order' => 5,
            'permission' => 'manage_nav_menu'
        ]);

        MenuItem::create([
            'title' => 'System Update',
            'url' => 'route:admin.update.index',
            'parent_id' => $admin->id,
            'order' => 6,
            'permission' => 'manage_system_updates'
        ]);
    }
}