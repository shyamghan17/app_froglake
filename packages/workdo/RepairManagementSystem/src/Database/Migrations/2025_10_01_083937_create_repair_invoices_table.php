<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('repair_invoices')) {
            Schema::create('repair_invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_id')->nullable();
                $table->decimal('repair_charge', 10, 2)->nullable();
                $table->decimal('total_amount', 10, 2)->nullable();
                $table->decimal('paid_amount', 10, 2)->default(0);
                $table->string('status')->default('0');
                $table->foreignId('repair_id')->nullable()->constrained('repair_order_requests')->onDelete('set null');
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
        Schema::dropIfExists('repair_invoices');
    }
};
