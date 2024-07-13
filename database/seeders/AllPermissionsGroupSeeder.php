<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Permission;

class AllPermissionsGroupSeeder extends Seeder
{
    public function run()
    {
        // Create the group
        $adminGroup = Group::create([
            'name' => 'Admin',
            'weight' => 1,
        ]);

        // Assign all permissions to the group
        $permissions = Permission::all();
        $adminGroup->permissions()->attach($permissions);
    }
}
