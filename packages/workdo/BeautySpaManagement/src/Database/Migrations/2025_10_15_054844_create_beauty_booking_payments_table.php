<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if(!Schema::hasTable('beauty_booking_payments'))
        {
            Schema::create('beauty_booking_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id');
                $table->text('total_person')->nullable();
                $table->integer('service')->nullable();
                $table->decimal('payment_amount', 15, 2);
                $table->date('payment_date');
                $table->text('description')->nullable();
                $table->string('customer_name');
                $table->string('reference_number');
                $table->foreignId('bank_account_id')->nullable();
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();

                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('set null');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('booking_id')->references('id')->on('beauty_bookings')->onDelete('cascade');
                $table->timestamps();

            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('beauty_booking_payments');
    }
};
