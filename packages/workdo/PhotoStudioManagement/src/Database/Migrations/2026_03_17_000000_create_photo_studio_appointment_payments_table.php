<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('photo_studio_appointment_payments')) {
            Schema::create('photo_studio_appointment_payments', function (Blueprint $table) {
                $table->id();
                $table->string('appointment_number');
                $table->string('customer_name');
                $table->string('service_name');
                $table->date('payment_date');
                $table->decimal('amount', 10, 2);
                $table->enum('payment_status', ['pending', 'cleared'])->default('pending');
                $table->string('payment_type')->default('offline');
                $table->longText('description')->nullable();
                $table->foreignId('bank_account_id')->nullable();
                $table->foreignId('appointment_id')->nullable()->constrained('photo_studio_appointments')->onDelete('set null');
                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('set null');
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
        Schema::dropIfExists('photo_studio_appointment_payments');
    }
};
