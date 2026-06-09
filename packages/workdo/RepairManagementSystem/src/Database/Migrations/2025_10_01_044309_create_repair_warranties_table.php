<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('repair_warranties')) {
            Schema::create('repair_warranties', function (Blueprint $table) {
                $table->id();
                $table->string('warranty_number');
                $table->string('warranty_period')->nullable();
                $table->longText('warranty_terms')->nullable();
                $table->string('claim_status')->default('0');
                $table->foreignId('repair_order_id')->nullable()->constrained('repair_order_requests')->onDelete('set null');
                $table->foreignId('part_id')->nullable()->constrained('repair_parts')->onDelete('set null');
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
        Schema::dropIfExists('repair_warranties');
    }
};
