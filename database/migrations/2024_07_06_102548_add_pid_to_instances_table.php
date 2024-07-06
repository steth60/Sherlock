<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPidToInstancesTable extends Migration
{
    public function up()
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->integer('pid')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->dropColumn('pid');
        });
    }
}
