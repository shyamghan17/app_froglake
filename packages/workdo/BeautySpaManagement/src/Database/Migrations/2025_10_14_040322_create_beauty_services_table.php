<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('beauty_services')) {
            Schema::create('beauty_services', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('max_bookable_persons')->nullable();
                $table->decimal('price', 10, 2)->nullable();
                $table->string('time')->nullable();
                $table->longText('description')->nullable();
                $table->string('service_image')->nullable();
                $table->foreignId('service_type_id')->nullable()->constrained('beauty_service_types')->onDelete('set null');
                $table->foreignId('staff_id')->nullable()->index();
                $table->json('included_services')->nullable();
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();

                $table->foreign('staff_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('beauty_services');
    }
};
