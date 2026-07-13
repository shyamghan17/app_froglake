<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('notice_reads')) {
            Schema::create('notice_reads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('notice_id')->constrained('notices')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamp('read_at')->nullable();
                $table->timestamp('acknowledged_at')->nullable();

                $table->unique(['notice_id', 'user_id']);

                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_reads');
    }
};
