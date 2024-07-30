<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Create Users
        $adminUser = User::firstOrCreate(
            ['email' => 'stephan.stickley@gmail.com'],
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
        $adminGroup = Group::where('name', 'Super Admin')->first();
        $editorGroup = Group::where('name', 'Editor')->first();
        $viewerGroup = Group::where('name', 'Viewer')->first();

        $adminUser->groups()->syncWithoutDetaching($adminGroup->id);
        $editorUser->groups()->syncWithoutDetaching($editorGroup->id);
        $viewerUser->groups()->syncWithoutDetaching($viewerGroup->id);
    }
}
