<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('photo_studio_appointments')) {
            Schema::create('photo_studio_appointments', function (Blueprint $table) {
                $table->id();
                $table->string('appointment_number')->nullable();
                $table->string('name');
                $table->string('email');
                $table->string('mobile_no');
                $table->json('team_member_ids')->nullable();
                $table->datetime('booking_start_date');
                $table->datetime('booking_end_date');
                $table->foreignId('service_id')->constrained('photo_studio_services')->onDelete('cascade');
                $table->decimal('price', 10, 2);
                $table->enum('status', ['pending', 'scheduled', 'completed', 'cancelled'])->default('pending');
                $table->enum('payment_status', ['pending', 'confirmed'])->default('pending');
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
        Schema::dropIfExists('photo_studio_appointments');
    }
};
