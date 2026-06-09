<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('portfolios')) {
            Schema::create('portfolios', function (Blueprint $table) {
                $table->id();
                $table->string('slug');

                // Personal Information fields
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('role')->nullable();
                $table->string('experience_years')->nullable();
                $table->string('photo')->nullable();
                $table->text('education')->nullable();

                // Work Details fields
                $table->string('title');
                $table->text('description')->nullable();
                $table->foreignId('category_id')->nullable();
                $table->string('client')->nullable();
                $table->string('live_url')->nullable();
                $table->string('repository_url')->nullable();
                $table->json('skills')->nullable();
                $table->string('duration')->nullable();
                $table->integer('team_size')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('budget')->nullable();
                $table->string('industry')->nullable();

                // Overview fields
                $table->boolean('show_overview')->default(true);
                $table->longText('overview')->nullable();

                // Gallery fields
                $table->boolean('show_gallery')->default(true);
                $table->json('images')->nullable();
                $table->string('video_link')->nullable();

                // Contact Section fields
                $table->boolean('show_contact')->default(true);
                $table->string('contact_heading')->nullable();
                $table->string('contact_message')->nullable();

                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();

                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('category_id')->references('id')->on('portfolio_categories')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
