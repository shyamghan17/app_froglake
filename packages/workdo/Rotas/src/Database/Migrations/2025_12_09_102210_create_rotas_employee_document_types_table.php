<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employee_document_types')) {
            Schema::create('employee_document_types', function (Blueprint $table) {
                $table->id();
                $table->string('document_name');
                $table->longText('description')->nullable();
                $table->boolean('is_required')->default(false);
                $table->unsignedBigInteger('creator_id')->nullable()->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();
                
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_document_types');
    }
};
