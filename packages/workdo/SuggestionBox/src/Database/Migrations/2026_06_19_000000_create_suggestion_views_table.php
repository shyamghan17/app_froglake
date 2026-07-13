<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('suggestion_views')) {
            Schema::create('suggestion_views', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('suggestion_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                $table->unique(['suggestion_id', 'user_id'], 'unique_suggestion_user_view');

                $table->foreign('suggestion_id')->references('id')->on('suggestions')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('suggestion_views');
    }
};