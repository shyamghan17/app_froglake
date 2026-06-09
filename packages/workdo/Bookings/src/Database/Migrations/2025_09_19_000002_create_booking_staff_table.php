<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('booking_staff')) {
            Schema::create('booking_staff', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('staff_id')->nullable();
                $table->text('item_ids')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('creator_id')->nullable();
                $table->timestamps();

                $table->foreign('staff_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('booking_staff');
    }
};