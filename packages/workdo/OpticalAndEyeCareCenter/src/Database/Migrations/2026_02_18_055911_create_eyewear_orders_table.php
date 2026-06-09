<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('eyewear_orders')) {
            Schema::create('eyewear_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number');
                $table->date('order_date');
                $table->foreignId('patient_id')->constrained('eye_patients')->onDelete('cascade');
                $table->bigInteger('warehouse_id')->nullable()->index();
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->decimal('balance_amount', 15, 2)->default(0);
                $table->enum('payment_status', ['draft', 'paid'])->default('draft');
                $table->string('payment_method', 50)->nullable();
                $table->foreignId('bank_account_id')->nullable();
                $table->decimal('extra_charge', 15, 2)->nullable();
                $table->date('delivery_date')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->longText('prescription_details')->nullable();
                $table->longText('special_notes')->nullable();

                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('set null');
                $table->foreignId('creator_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->index('patient_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('eyewear_orders');
    }
};
