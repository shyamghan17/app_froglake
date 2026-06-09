<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if(!Schema::hasTable('eye_test_prescriptions'))
        {
            Schema::create('eye_test_prescriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('eye_patients')->onDelete('cascade');
                $table->foreignId('doctor_name')->nullable()->index();
                $table->date('test_date');
                $table->longText('test_results')->nullable();
                $table->longText('prescription_details')->nullable();
                $table->date('prescription_expiry_date')->nullable();
                $table->longText('notes')->nullable();
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();

                $table->foreign('doctor_name')->references('id')->on('users')->onDelete('set null');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('eye_test_prescriptions');
    }
};
