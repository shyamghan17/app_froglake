<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rotas_availabilities')) {
            Schema::create('rotas_availabilities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->nullable()->index();
                $table->string('name');
                $table->date('start_date');
                $table->date('end_date');
                $table->json('availability');
                $table->unsignedBigInteger('creator_id')->nullable()->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rotas_availabilities');
    }
};