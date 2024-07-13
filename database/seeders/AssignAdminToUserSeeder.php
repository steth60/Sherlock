<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AssignAdminToUserSeeder extends Seeder
{
    public function run()
    {
        // Find the user with ID 26
        $user = User::find(26);

        if (!$user) {
            Log::error('User with ID 26 not found.');
            return;
        }

        // Find the admin group
        $adminGroup = Group::where('name', 'Admin')->first();

        if (!$adminGroup) {
            Log::error('Admin group not found.');
            return;
        }

        // Assign the admin group to the user
        $user->groups()->syncWithoutDetaching($adminGroup);

        // Log the assignment
        Log::info('User assigned to admin group', ['user_id' => $user->id, 'group_id' => $adminGroup->id]);
    }
}
