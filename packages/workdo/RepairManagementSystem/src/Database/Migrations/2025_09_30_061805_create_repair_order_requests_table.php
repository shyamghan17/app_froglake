<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('repair_order_requests')) {
            Schema::create('repair_order_requests', function (Blueprint $table) {
                $table->id();
                $table->string('product_name');
                $table->bigInteger('product_quantity')->nullable();
                $table->string('customer_name');
                $table->string('customer_email');
                $table->string('customer_mobile_no', 20)->nullable();
                $table->date('date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->unsignedBigInteger('repair_technician')->nullable();
                $table->string('location')->nullable();
                $table->integer('status')->default(0)->comment('0 = Pending, 1= Start Repair, 2= End Repair, 3= Start Testing, 4= End Testing, 5= Irrepairable, 6= Cancel');
                $table->foreignId('creator_id')->nullable()->index();
                $table->foreignId('created_by')->nullable()->index();

                $table->foreign('repair_technician')->references('id')->on('repair_technicians')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_order_requests');
    }
};
