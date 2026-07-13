<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('device_protocol_communication_logs')) {
            Schema::create('device_protocol_communication_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('device_protocol_device_id')->nullable();
                $table->string('device_sn')->nullable();
                $table->string('protocol')->nullable();
                $table->string('endpoint')->nullable();
                $table->string('method', 16)->nullable();
                $table->string('ip', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->json('request_query')->nullable();
                $table->unsignedInteger('request_size')->nullable();
                $table->longText('payload_excerpt')->nullable();
                $table->unsignedSmallInteger('response_status')->nullable();
                $table->unsignedInteger('response_size')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        $this->ensureIndexes();
    }

    public function down(): void
    {
        Schema::dropIfExists('device_protocol_communication_logs');
    }

    private function ensureIndexes(): void
    {
        Schema::table('device_protocol_communication_logs', function (Blueprint $table) {
            if (!$this->indexExists('device_protocol_communication_logs', 'dpcl_created_by_idx')) {
                $table->index('created_by', 'dpcl_created_by_idx');
            }

            if (!$this->indexExists('device_protocol_communication_logs', 'dpcl_device_id_idx')) {
                $table->index('device_protocol_device_id', 'dpcl_device_id_idx');
            }

            if (!$this->indexExists('device_protocol_communication_logs', 'dpcl_device_sn_idx')) {
                $table->index('device_sn', 'dpcl_device_sn_idx');
            }

            if (!$this->indexExists('device_protocol_communication_logs', 'dpcl_endpoint_idx')) {
                $table->index('endpoint', 'dpcl_endpoint_idx');
            }

            if (!$this->indexExists('device_protocol_communication_logs', 'dpcl_status_idx')) {
                $table->index('response_status', 'dpcl_status_idx');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);

        return !empty($indexes);
    }
};
