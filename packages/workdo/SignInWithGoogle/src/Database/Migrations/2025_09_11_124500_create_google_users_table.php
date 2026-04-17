<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('google_users')) {
            Schema::create('google_users', function (Blueprint $table) {
                $table->id();
                $table->string('google_id')->unique();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('email', 255)->nullable();
                $table->string('name')->nullable();
                $table->string('avatar')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['google_id', 'user_id']);
                $table->index('email');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('google_users');
    }
};