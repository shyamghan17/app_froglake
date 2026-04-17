<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if(!Schema::hasTable('findgooglelead_founded_lead_contacts'))
        {
            Schema::create('findgooglelead_founded_lead_contacts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('founded_lead_id')->nullable();
                $table->string('is_lead')->nullable();
                $table->string('is_sync')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('mobile_no')->nullable();
                $table->string('website')->nullable();
                $table->longText('address')->nullable();
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();
                $table->timestamps();

                $table->foreign('founded_lead_id')->references('id')->on('findgooglelead_founded_leads')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('findgooglelead_founded_lead_contacts');
    }
};