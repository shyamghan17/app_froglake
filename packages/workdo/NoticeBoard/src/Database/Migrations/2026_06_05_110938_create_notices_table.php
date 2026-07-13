<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('notices')) {
            Schema::create('notices', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->longText('description')->nullable();
                $table->json('attachments')->nullable();
                $table->date('start_date');
                $table->date('expiry_date')->nullable();
                $table->boolean('is_pinned')->default(false);
                $table->enum('priority', ['normal', 'urgent', 'critical'])->default('normal');
                $table->boolean('require_acknowledgment')->default(false);
                $table->enum('target_type', ['all', 'department', 'role', 'specific_users'])->default('all');
                $table->boolean('allow_comments')->default(false);
                $table->enum('status', ['draft', 'published', 'deactivated'])->default('draft');

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
        Schema::dropIfExists('notices');
    }
};
