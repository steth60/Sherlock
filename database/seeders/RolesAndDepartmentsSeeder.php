<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Department;

class RolesAndDepartmentsSeeder extends Seeder
{
    public function run()
    {
        $roles = ['Super Admin', 'Admin', 'User'];
        $departments = ['IT Support', 'Professional Services', 'Operations Team', 'Marketing', 'Sales'];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        foreach ($departments as $department) {
            Department::create(['name' => $department]);
        }
    }
}
