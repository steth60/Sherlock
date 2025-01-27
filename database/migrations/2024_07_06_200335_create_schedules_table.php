<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instance_id');
            $table->string('action'); // start, stop, restart
            $table->json('months');
            $table->json('days');
            $table->json('hours');
            $table->json('minutes');
            $table->string('description')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->foreign('instance_id')->references('id')->on('instances')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
