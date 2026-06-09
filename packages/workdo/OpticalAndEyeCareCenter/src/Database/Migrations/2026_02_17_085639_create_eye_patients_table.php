<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if(!Schema::hasTable('eye_patients'))
        {
            Schema::create('eye_patients', function (Blueprint $table) {
                $table->id();
                $table->string('patient_name');
                $table->date('dob')->nullable();
                $table->string('gender')->default('0');
                $table->string('contact_no', 20)->nullable();
                $table->longText('address')->nullable();
                $table->longText('medical_history')->nullable();
                $table->longText('previous_prescriptions')->nullable();
                $table->foreignId('preferred_doctor')->nullable()->index();
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();

                $table->foreign('preferred_doctor')->references('id')->on('users')->onDelete('set null');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('eye_patients');
    }
};
