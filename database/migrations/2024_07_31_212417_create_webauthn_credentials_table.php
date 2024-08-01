<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebauthnCredentialsTable extends Migration
{
    public function up()
    {
        Schema::create('webauthn_credentials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('credential_id')->unique();
            $table->binary('public_key');
            $table->string('type');
            $table->integer('counter');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('webauthn_credentials');
    }
}
