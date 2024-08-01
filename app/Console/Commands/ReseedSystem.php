<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\GroupsSeeder;
use Database\Seeders\MenuItemsSeeder;

class ReseedSystem extends Command
{
    protected $signature = 'system:reseed';
    protected $description = 'Reseed permissions, groups, and menu items';

    public function handle()
    {
        $this->info('Starting system reseed...');

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the tables
        $this->truncateTables();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Run the seeders
        $this->runSeeders();

        $this->info('System reseed completed successfully!');
    }

    private function truncateTables()
    {
        $this->info('Truncating tables...');
        DB::table('permissions')->truncate();
        DB::table('groups')->truncate();
        DB::table('menu_items')->truncate();
        // If you have pivot tables, truncate them here as well
        // For example:
        // DB::table('group_permission')->truncate();
        $this->info('Tables truncated.');
    }

    private function runSeeders()
    {
        $this->info('Running PermissionsSeeder...');
        $this->call('db:seed', ['--class' => PermissionsSeeder::class]);

        $this->info('Running GroupsSeeder...');
        $this->call('db:seed', ['--class' => GroupsSeeder::class]);

        $this->info('Running MenuItemsSeeder...');
        $this->call('db:seed', ['--class' => MenuItemsSeeder::class]);
    }
}