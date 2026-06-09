<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('eyewear_order_items')) {
            Schema::create('eyewear_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('eyewear_orders')->onDelete('cascade');
                $table->foreignId('product_id')->nullable()->constrained('product_service_items')->onDelete('set null');
                $table->enum('item_type', ['standard', 'custom'])->default('standard');
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('discount_percentage', 5, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_percentage', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->timestamps();

                $table->index('order_id');
                $table->index('product_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('eyewear_order_items');
    }
};
