<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('notice_comments')) {
            Schema::create('notice_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('notice_id')->constrained('notices')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('parent_id')->nullable()->constrained('notice_comments')->onDelete('cascade');
                $table->text('comment');
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
        Schema::dropIfExists('notice_comments');
    }
};
