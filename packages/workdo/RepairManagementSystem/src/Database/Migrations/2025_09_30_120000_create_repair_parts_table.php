<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('repair_parts')) {
            Schema::create('repair_parts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('repair_id')->nullable()->index();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->integer('quantity')->nullable();
                $table->text('tax')->nullable();
                $table->float('discount')->default('0.00');
                $table->string('description')->nullable();
                $table->float('price')->default('0.00');
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();

                $table->foreign('repair_id')->references('id')->on('repair_order_requests')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('product_service_items')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_parts');
    }
};