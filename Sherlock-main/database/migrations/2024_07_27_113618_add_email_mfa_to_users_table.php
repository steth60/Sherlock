<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailMfaToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('two_factor_email_enabled')->default(false)->after('two_factor_enabled');
            $table->string('email_mfa_code')->nullable()->after('two_factor_recovery_codes');
            $table->timestamp('email_mfa_code_expires_at')->nullable()->after('email_mfa_code');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('two_factor_email_enabled');
            $table->dropColumn('email_mfa_code');
            $table->dropColumn('email_mfa_code_expires_at');
        });
    }
}