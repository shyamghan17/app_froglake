<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('beauty_loyalty_programs')) {
            Schema::create('beauty_loyalty_programs', function (Blueprint $table) {
                $table->id();
                $table->string('customer_name');
                $table->integer('points_earned')->nullable();
                $table->integer('points_redeemed')->nullable();
                $table->date('last_updated')->nullable();

                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();

                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('beauty_loyalty_programs');
    }
};
