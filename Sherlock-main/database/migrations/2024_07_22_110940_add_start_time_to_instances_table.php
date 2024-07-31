<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->timestamp('start_time')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->dropColumn('start_time');
        });
    }    
};
