<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if(!Schema::hasTable('eye_care_appoinments'))
        {
            Schema::create('eye_care_appoinments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('eye_patients')->onDelete('cascade');
                $table->foreignId('doctor_name')->nullable()->index();
                $table->timestamp('appointment_datetime');
                $table->string('status')->default('0');
                $table->string('appointment_type')->default('0');
                $table->longText('notes')->nullable();
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();

                $table->foreign('doctor_name')->references('id')->on('users')->onDelete('set null');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->index('appointment_datetime');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('eye_care_appoinments');
    }
};