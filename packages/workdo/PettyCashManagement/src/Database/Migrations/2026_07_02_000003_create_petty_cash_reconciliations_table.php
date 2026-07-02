<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('petty_cash_reconciliations')) {
            Schema::create('petty_cash_reconciliations', function (Blueprint $table) {
                $table->id();
                $table->date('period_start')->index();
                $table->date('period_end')->index();
                $table->decimal('opening_balance', 10, 2)->default(0);
                $table->decimal('additions_total', 10, 2)->default(0);
                $table->decimal('expenses_total', 10, 2)->default(0);
                $table->decimal('expected_closing', 10, 2)->default(0);
                $table->decimal('counted_cash', 10, 2)->default(0);
                $table->decimal('variance', 10, 2)->default(0);
                $table->boolean('locked')->default(false)->index();

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
        Schema::dropIfExists('petty_cash_reconciliations');
    }
};

