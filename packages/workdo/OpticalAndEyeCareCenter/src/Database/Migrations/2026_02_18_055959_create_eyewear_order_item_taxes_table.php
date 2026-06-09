<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('eyewear_order_item_taxes')) {
            Schema::create('eyewear_order_item_taxes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')->constrained('eyewear_order_items')->onDelete('cascade');
                $table->string('tax_name');
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->timestamps();

                $table->index('item_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('eyewear_order_item_taxes');
    }
};
