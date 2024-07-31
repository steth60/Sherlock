<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuItemsSeeder extends Seeder
{
    public function run()
    {
        // Service Desk Tools
        $serviceDeskTools = MenuItem::create([
            'title' => 'Service Desk Tools',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-tools',
            'order' => 1,
            'permission' => 'View-service-desk-tools'
        ]);

        MenuItem::create([
            'title' => 'Ticket Management',
            'url' => '#',
            'parent_id' => $serviceDeskTools->id,
            'order' => 1,
            'permission' => 'View-ticket-management'
        ]);

        MenuItem::create([
            'title' => 'Task & Project',
            'url' => '#',
            'parent_id' => $serviceDeskTools->id,
            'order' => 2,
            'permission' => 'View-task-project'
        ]);

        // App Contents
        $appContents = MenuItem::create([
            'title' => 'App Contents',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-apps',
            'order' => 2,
            'permission' => 'View-app-contents'
        ]);

        MenuItem::create([
            'title' => 'Python Instances',
            'url' => '#',
            'parent_id' => $appContents->id,
            'order' => 1,
            'permission' => 'View-python-instances'
        ]);

        MenuItem::create([
            'title' => 'Docker Instances',
            'url' => '#',
            'parent_id' => $appContents->id,
            'order' => 2,
            'permission' => 'View-docker-instances'
        ]);

        MenuItem::create([
            'title' => 'VM Instances',
            'url' => '#',
            'parent_id' => $appContents->id,
            'order' => 3,
            'permission' => 'View-vm-instances'
        ]);

        // Tasks
        $tasks = MenuItem::create([
            'title' => 'Tasks',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-check-circle-outline',
            'order' => 3,
            'permission' => 'View-tasks'
        ]);

        MenuItem::create([
            'title' => 'Dashboard',
            'url' => '#',
            'parent_id' => $tasks->id,
            'order' => 1,
            'permission' => 'View-tasks-dashboard'
        ]);

        MenuItem::create([
            'title' => 'Task',
            'url' => '#',
            'parent_id' => $tasks->id,
            'order' => 2,
            'permission' => 'View-tasks-task'
        ]);

        // Settings
        $settings = MenuItem::create([
            'title' => 'Settings',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-settings',
            'order' => 4,
            'permission' => 'View-settings'
        ]);

        MenuItem::create([
            'title' => 'Personal Information',
            'url' => '#',
            'parent_id' => $settings->id,
            'order' => 1,
            'permission' => 'View-personal-information'
        ]);

        MenuItem::create([
            'title' => 'Account Settings',
            'url' => '#',
            'parent_id' => $settings->id,
            'order' => 2,
            'permission' => 'View-account-settings'
        ]);

        // Admin
        $admin = MenuItem::create([
            'title' => 'Admin',
            'url' => 'javascript:void(0)',
            'icon' => 'mdi mdi-shield-account',
            'order' => 5,
            'permission' => 'View-admin'
        ]);

        MenuItem::create([
            'title' => 'User Management',
            'url' => '#',
            'parent_id' => $admin->id,
            'order' => 1,
            'permission' => 'View-user-management'
        ]);

        MenuItem::create([
            'title' => 'Group Management',
            'url' => '#',
            'parent_id' => $admin->id,
            'order' => 2,
            'permission' => 'View-group-management'
        ]);

        MenuItem::create([
            'title' => 'Nav Menu',
            'url' => '#',
            'parent_id' => $admin->id,
            'order' => 3,
            'permission' => 'View-nav-menu'
        ]);

        MenuItem::create([
            'title' => 'Maintain Mode',
            'url' => '#',
            'parent_id' => $admin->id,
            'order' => 4,
            'permission' => 'View-maintain-mode'
        ]);

        MenuItem::create([
            'title' => 'Update',
            'url' => '#',
            'parent_id' => $admin->id,
            'order' => 5,
            'permission' => 'View-update'
        ]);
    }
}
