<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('booking_business_hours')) {
            Schema::create('booking_business_hours', function (Blueprint $table) {
                $table->id();
                $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
                $table->boolean('is_closed')->default(false);
                $table->json('time_slots')->nullable(); // Store multiple time slots like [{"open":"09:30","close":"11:00"},{"open":"15:00","close":"17:00"}]
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('creator_id')->nullable();
                $table->timestamps();
                
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('booking_business_hours');
    }
};