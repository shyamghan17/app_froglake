<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if(!Schema::hasTable('eyewear_items'))
        {
            Schema::create('eyewear_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->nullable()->index();
                $table->string('product_type')->nullable();
                $table->string('brand_name')->nullable();
                $table->text('prescription_detail')->nullable();
                $table->enum('numbering_status', ['numbering', 'non-numbering'])->default('numbering');
                $table->longText('customization_details')->nullable();

                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();

                $table->foreign('product_id')->references('id')->on('product_service_items')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('eyewear_items');
    }
};
