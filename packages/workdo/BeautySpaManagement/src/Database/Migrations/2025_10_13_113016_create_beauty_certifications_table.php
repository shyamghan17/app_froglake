<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('beauty_certifications')) {
            Schema::create('beauty_certifications', function (Blueprint $table) {
                $table->id();
                $table->string('employee_name');
                $table->string('certificate_name');
                $table->date('issued_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->foreignId('training_id')->nullable()->constrained('beauty_trainings')->onDelete('set null');
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
        Schema::dropIfExists('beauty_certifications');
    }
};
