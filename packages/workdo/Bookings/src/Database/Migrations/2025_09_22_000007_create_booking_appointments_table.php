<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('booking_appointments')) {
            Schema::create('booking_appointments', function (Blueprint $table) {
                $table->id();
                $table->string('appointment_number')->unique();
                $table->date('date');
                $table->unsignedBigInteger('item_id')->nullable();
                $table->unsignedBigInteger('package_id')->nullable();
                $table->unsignedBigInteger('staff_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->string('payment')->nullable();
                $table->string('payment_status')->nullable()->default('pending');
                $table->string('payment_receipt')->nullable();
                $table->string('online_payment_id')->nullable();
                $table->string('status')->default('pending');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('creator_id')->nullable();
                $table->timestamps();

                $table->foreign('item_id')->references('id')->on('product_service_items')->onDelete('set null');
                $table->foreign('package_id')->references('id')->on('booking_packages')->onDelete('set null');
                $table->foreign('staff_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('customer_id')->references('id')->on('booking_customers')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('booking_appointments');
    }
};