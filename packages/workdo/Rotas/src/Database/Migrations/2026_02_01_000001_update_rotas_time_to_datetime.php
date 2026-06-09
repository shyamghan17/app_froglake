<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rotas')) {
            Schema::table('rotas', function (Blueprint $table) {
                $table->dateTime('start_time')->nullable()->change();
                $table->dateTime('end_time')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('rotas')) {
            Schema::table('rotas', function (Blueprint $table) {
                $table->time('start_time')->nullable()->change();
                $table->time('end_time')->nullable()->change();
            });
        }
    }
};
