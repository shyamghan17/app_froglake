<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('beauty_gift_cards')) {
            Schema::create('beauty_gift_cards', function (Blueprint $table) {
                $table->id();
                $table->string('card_code');
                $table->string('customer');
                $table->decimal('balance', 10, 2)->nullable();
                $table->date('expiry_date')->nullable();
                $table->boolean('status')->default(true);

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
        Schema::dropIfExists('beauty_gift_cards');
    }
};
