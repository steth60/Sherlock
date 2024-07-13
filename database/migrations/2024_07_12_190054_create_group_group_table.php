<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupGroupTable extends Migration
{
    public function up()
    {
        Schema::create('group_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('child_id');
            $table->foreign('parent_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('child_id')->references('id')->on('groups')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_group');
    }
}
