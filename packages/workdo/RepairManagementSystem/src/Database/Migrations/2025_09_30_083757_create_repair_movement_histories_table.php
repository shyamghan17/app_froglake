<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('repair_movement_histories')) {
            Schema::create('repair_movement_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('repair_order_request_id')->nullable()->constrained('repair_order_requests')->onDelete('cascade');
                $table->timestamp('date_time')->nullable();
                $table->string('movement_from')->nullable();
                $table->string('movement_to')->nullable();
                $table->string('movement_reason')->nullable();
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
        Schema::dropIfExists('repair_movement_histories');
    }
};