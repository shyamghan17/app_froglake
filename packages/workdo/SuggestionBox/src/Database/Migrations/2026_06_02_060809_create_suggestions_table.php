<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('suggestions')) {
            Schema::create('suggestions', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->longText('description');
                $table->foreignId('category_id')->nullable()->constrained('suggestion_categories')->onDelete('set null');
                $table->enum('status', ['new', 'under_review', 'accepted', 'rejected', 'complete'])->default('new');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->boolean('is_anonymous')->default(false);
                $table->integer('votes_count')->default(0);
                $table->integer('views_count')->default(0);
                $table->longText('admin_response')->nullable();
                $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
                $table->string('responded_at')->nullable();
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
        Schema::dropIfExists('suggestions');
    }
};
