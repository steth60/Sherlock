<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            //PermissionsSeeder::class,
            //MenuItemsSeeder::class,
            //GroupsAndUsersSeeder::class,
            //AllPermissionsGroupSeeder::class,
            //AssignAdminToUserSeeder::class,
        ]);
    }
}
