<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('single_sms')) {
            Schema::create('single_sms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contact_id')->nullable()->constrained('bulk_sms_contacts')->onDelete('set null');
                $table->string('mobile_no');
                $table->string('status')->default('0');
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
        Schema::dropIfExists('single_sms');
    }
};
