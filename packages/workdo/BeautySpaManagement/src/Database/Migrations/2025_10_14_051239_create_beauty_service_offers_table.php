<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('beauty_service_offers')) {
            Schema::create('beauty_service_offers', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('name');
                $table->decimal('price', 10, 2)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->decimal('discount', 10, 2)->nullable();
                $table->decimal('offer_price', 10, 2)->nullable();
                $table->longText('description')->nullable();
                $table->foreignId('beauty_service_id')->nullable()->constrained('beauty_services')->onDelete('set null');
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
        Schema::dropIfExists('beauty_service_offers');
    }
};
