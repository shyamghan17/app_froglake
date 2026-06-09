<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mail_box_credentials')) {
            Schema::create('mail_box_credentials', function (Blueprint $table) {
                $table->id();
                $table->string('mail_driver')->default('smtp');
                $table->string('email');
                $table->string('password');
                $table->string('imap_host');
                $table->integer('imap_port')->default(993);
                $table->string('imap_encryption')->default('ssl');
                $table->string('smtp_host');
                $table->integer('smtp_port')->default(587);
                $table->string('smtp_encryption')->default('tls');
                $table->string('from_name')->nullable();
                $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('mail_box_credentials');
    }
};