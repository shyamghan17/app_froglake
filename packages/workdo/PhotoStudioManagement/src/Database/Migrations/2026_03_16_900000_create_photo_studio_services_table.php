<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('photo_studio_services')) {
            Schema::create('photo_studio_services', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->json('service_category_ids');
                $table->longText('description')->nullable();
                $table->string('image')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->boolean('status')->default(true);
                $table->json('camera_kit_ids')->nullable();
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
        Schema::dropIfExists('photo_studio_services');
    }
};
