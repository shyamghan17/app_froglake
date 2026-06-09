<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bulksms_send_messages')) {
            Schema::create('bulksms_send_messages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('group_id')->nullable()->constrained('bulk_sms_groups')->onDelete('set null');
                $table->string('mobile_no');
                $table->longText('sms');
                $table->string('status')->default('0');
                
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
        Schema::dropIfExists('bulksms_send_messages');
    }
};