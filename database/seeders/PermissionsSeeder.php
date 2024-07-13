<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'View-dashboard',
            'View-Instance-page',
            'Create-Instance',
            'Stop-Instances',
            'Edit-Instances',
            'Start-Instance',
            'Delete-Instance',
            'Update-Instance',
            'View-Instance-files',
            'Edit-Instance-files',
            'View-Instance-notes',
            'Create-Instance-notes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
