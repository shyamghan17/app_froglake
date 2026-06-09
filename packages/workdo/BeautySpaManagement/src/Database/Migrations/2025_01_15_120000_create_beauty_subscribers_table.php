<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('beauty_subscribers')) {
            Schema::create('beauty_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->foreignId('created_by')->index();

                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('beauty_subscribers');
    }
};
