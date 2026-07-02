<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('petty_cash_audit_logs')) {
            Schema::create('petty_cash_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->string('action');
                $table->string('subject_type')->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->foreignId('actor_id')->nullable()->constrained('users')->onDelete('set null');
                $table->json('meta')->nullable();
                $table->foreignId('created_by')->nullable()->index();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->index(['created_by', 'subject_type', 'subject_id']);
                $table->index(['created_by', 'action']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_audit_logs');
    }
};

