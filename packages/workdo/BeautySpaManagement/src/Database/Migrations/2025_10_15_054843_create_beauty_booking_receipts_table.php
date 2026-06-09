<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('beauty_booking_receipts')) {
            Schema::create('beauty_booking_receipts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('beauty_booking_id')->nullable()->constrained('beauty_bookings')->onDelete('cascade');
                $table->string('name')->nullable();
                $table->string('service')->nullable();
                $table->string('number')->nullable();
                $table->string('gender')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->string('price')->nullable();
                $table->string('payment_type')->nullable();
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
        Schema::dropIfExists('beauty_booking_receipts');
    }
};
