<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rotas')) {
            Schema::create('rotas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->date('rotas_date')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->integer('break_time')->default(0)->comment('Break time in minutes');
                $table->integer('time_diff_in_minutes')->default(0)->comment('Total shift duration in minutes');
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->unsignedBigInteger('department_id')->nullable()->index();
                $table->unsignedBigInteger('designation_id')->nullable()->index();
                $table->unsignedBigInteger('shift_id')->nullable()->index();
                $table->enum('type', ['shift', 'dayoff', 'leave'])->default('shift');
                $table->boolean('is_published')->default(false);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('issued_by')->nullable()->index();
                $table->unsignedBigInteger('creator_id')->nullable()->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
                $table->foreign('designation_id')->references('id')->on('designations')->onDelete('set null');
                $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
                $table->foreign('issued_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rotas');
    }
};