<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('portfolio_custom_sections')) {
            Schema::create('portfolio_custom_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('portfolio_id')->constrained('portfolios')->onDelete('cascade');
                $table->string('title');
                $table->longText('content');
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['portfolio_id', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_custom_sections');
    }
};
