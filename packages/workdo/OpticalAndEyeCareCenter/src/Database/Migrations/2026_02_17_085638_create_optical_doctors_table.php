<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('hospital_doctors')) {
            Schema::create('hospital_doctors', function (Blueprint $table) {
                $table->id();
                $table->string('doctor_code');
                $table->string('license_number');
                $table->string('gender');
                $table->integer('years_of_experience')->nullable();
                $table->decimal('consultation_fee', 10, 2)->nullable();
                $table->longText('qualifications')->nullable();
                $table->string('status')->default('0');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->foreignId('hospital_specialization_id')->nullable()->constrained('hospital_specializations')->onDelete('set null');
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
        Schema::dropIfExists('hospital_doctors');
    }
};
