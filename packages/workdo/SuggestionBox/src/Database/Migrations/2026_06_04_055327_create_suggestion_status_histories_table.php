<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('suggestion_status_histories')) {
            Schema::create('suggestion_status_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('suggestion_id')->nullable()->constrained('suggestions')->onDelete('set null');
                $table->enum('old_status', ['new', 'under_review', 'accepted', 'rejected', 'complete'])->default('new');
                $table->enum('new_status', ['new', 'under_review', 'accepted', 'rejected', 'complete'])->default('new');
                $table->longText('comment')->nullable();
                $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('creator_id')->nullable();
                $table->foreignId('created_by')->nullable();

                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('suggestion_status_histories');
    }
};
