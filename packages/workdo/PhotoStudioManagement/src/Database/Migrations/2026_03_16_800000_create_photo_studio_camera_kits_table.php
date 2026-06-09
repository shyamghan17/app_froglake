<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('photo_studio_camera_kits')) {
            Schema::create('photo_studio_camera_kits', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('image');
                $table->longText('description');
                $table->json('tags');
                $table->json('specifications');
                $table->foreignId('equipment_type_id')->constrained('photo_studio_equipment_types')->onDelete('cascade');
                $table->enum('status', ['available', 'unavailable'])->default('available');
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
        Schema::dropIfExists('photo_studio_camera_kits');
    }
};
