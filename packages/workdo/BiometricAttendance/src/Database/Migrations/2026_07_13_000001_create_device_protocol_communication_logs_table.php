<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('device_protocol_communication_logs')) {
            Schema::create('device_protocol_communication_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->unsignedBigInteger('device_protocol_device_id')->nullable()->index();
                $table->string('device_sn')->nullable()->index();
                $table->string('protocol')->nullable();
                $table->string('endpoint')->nullable()->index();
                $table->string('method', 16)->nullable();
                $table->string('ip', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->json('request_query')->nullable();
                $table->unsignedInteger('request_size')->nullable();
                $table->longText('payload_excerpt')->nullable();
                $table->unsignedSmallInteger('response_status')->nullable()->index();
                $table->unsignedInteger('response_size')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('device_protocol_communication_logs');
    }
};
