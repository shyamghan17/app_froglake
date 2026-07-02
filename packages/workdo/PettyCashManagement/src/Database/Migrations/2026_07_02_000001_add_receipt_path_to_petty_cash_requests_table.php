<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('petty_cash_requests') && !Schema::hasColumn('petty_cash_requests', 'receipt_path')) {
            Schema::table('petty_cash_requests', function (Blueprint $table) {
                $table->text('receipt_path')->nullable()->after('remarks');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('petty_cash_requests') && Schema::hasColumn('petty_cash_requests', 'receipt_path')) {
            Schema::table('petty_cash_requests', function (Blueprint $table) {
                $table->dropColumn('receipt_path');
            });
        }
    }
};

