<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('photo_studio_team_members')) {
            Schema::create('photo_studio_team_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->index();
                $table->string('designation');
                $table->integer('experience_year')->default(0);
                $table->string('skills')->nullable();
                $table->decimal('rate_per_hour', 8, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->longText('bio')->nullable();
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_studio_team_members');
    }
};
