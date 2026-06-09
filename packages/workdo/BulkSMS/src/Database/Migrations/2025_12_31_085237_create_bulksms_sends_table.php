<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bulksms_sends')) {
            Schema::create('bulksms_sends', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->nullable()->constrained('bulk_sms_groups')->onDelete('set null');
                $table->string('mobile_no');
                $table->longText('sms');
                
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
        Schema::dropIfExists('bulksms_sends');
    }
};