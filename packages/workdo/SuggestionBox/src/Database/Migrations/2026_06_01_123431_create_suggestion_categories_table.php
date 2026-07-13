<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('suggestion_categories')) {
            Schema::create('suggestion_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('color', 7)->default('#FF6B6B');
                $table->longText('description')->nullable();
                $table->boolean('is_active')->default(false);
                $table->integer('display_order')->nullable();

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
        Schema::dropIfExists('suggestion_categories');
    }
};
