<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('beauty_memberships')) {
            Schema::create('beauty_memberships', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('duration')->nullable();
                $table->string('benefits')->nullable();
                $table->decimal('price', 10, 2)->nullable();
                $table->longText('description')->nullable();
                $table->foreignId('included_services_id')->nullable()->constrained('beauty_services')->onDelete('set null');
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
        Schema::dropIfExists('beauty_memberships');
    }
};
