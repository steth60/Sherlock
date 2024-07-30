<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMfaColumnsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('physical_key_enabled')->default(false);
            $table->boolean('email_mfa_enabled')->default(false);
            
            // Only drop if column exists
            if (Schema::hasColumn('users', 'backup_email')) {
                $table->dropColumn('backup_email');
            }
            
            // Only drop if column exists
            if (Schema::hasColumn('users', 'login_notifications')) {
                $table->dropColumn('login_notifications');
            }
        });
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('physical_key_enabled');
            $table->dropColumn('email_mfa_enabled');
            
            // Only add if column doesn't exist
            if (!Schema::hasColumn('users', 'backup_email')) {
                $table->string('backup_email')->nullable();
            }
            
            // Only add if column doesn't exist
            if (!Schema::hasColumn('users', 'login_notifications')) {
                $table->boolean('login_notifications')->default(false);
            }
        });
    }
}
