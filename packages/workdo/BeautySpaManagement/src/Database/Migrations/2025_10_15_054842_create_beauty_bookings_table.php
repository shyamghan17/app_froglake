<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('beauty_bookings')) {
            Schema::create('beauty_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->integer('service');
                $table->date('date')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->integer('person')->nullable();
                $table->decimal('price', 10, 2)->nullable();
                $table->string('phone_number', 20)->nullable();
                $table->string('gender')->nullable();
                $table->string('reference')->nullable();
                $table->longText('notes')->nullable();
                $table->string('payment_option')->nullable();
                $table->enum('payment_status', ['pending', 'paid'])->default('pending');
                $table->integer('stage_id')->default(0);
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
        Schema::dropIfExists('beauty_bookings');
    }
};
