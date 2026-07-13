<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('notice_targets')) {
            Schema::create('notice_targets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('notice_id')->constrained('notices')->onDelete('cascade');
                $table->enum('target_type', ['department', 'role', 'specific_users']);

                $table->foreignId('department_id')->nullable()->index();
                $table->foreignId('role_id')->nullable()->index();
                $table->foreignId('user_id')->nullable()->index();

                $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_targets');
    }
};
