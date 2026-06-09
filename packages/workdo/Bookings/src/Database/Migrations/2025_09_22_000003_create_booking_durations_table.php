<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('booking_durations')) {
            Schema::create('booking_durations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id')->nullable();
                $table->string('duration')->nullable(); // Duration as time string (HH:MM)
                $table->integer('total_slots')->default(1); // Total available slots
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('creator_id')->nullable();
                $table->timestamps();

                $table->foreign('item_id')->references('id')->on('product_service_items')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('booking_durations');
    }
};