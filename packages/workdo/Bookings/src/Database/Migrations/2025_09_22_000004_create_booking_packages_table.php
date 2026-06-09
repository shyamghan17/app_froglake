<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('booking_packages')) {
            Schema::create('booking_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('item_id')->nullable();
                $table->foreign('item_id')->references('id')->on('product_service_items')->onDelete('cascade');
                $table->text('services')->nullable();
                $table->string('delivery_time')->nullable();
                $table->string('delivery_period'); // minutes, hours
                $table->decimal('price', 10, 2);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('creator_id')->nullable();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('booking_packages');
    }
};