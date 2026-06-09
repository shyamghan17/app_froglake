<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->string('employee_id');
                $table->date('date_of_birth')->nullable();
                $table->string('gender')->default('Male');
                $table->unsignedBigInteger('shift')->nullable()->index();
                $table->string('attendance_policy')->nullable();
                $table->date('date_of_joining')->nullable();
                $table->string('employment_type')->default('0');
                $table->string('address_line_1')->nullable();
                $table->string('address_line_2')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('emergency_contact_name')->nullable();
                $table->string('emergency_contact_relationship')->nullable();
                $table->string('emergency_contact_number', 20)->nullable();
                $table->string('bank_name')->nullable();
                $table->string('account_holder_name')->nullable();
                $table->string('account_number')->nullable();
                $table->string('bank_identifier_code')->nullable();
                $table->string('bank_branch')->nullable();
                $table->string('tax_payer_id')->nullable();
                $table->decimal('basic_salary', 10, 2)->nullable();
                $table->decimal('hours_per_day', 8, 2)->nullable();
                $table->decimal('days_per_week', 8, 2)->nullable();
                $table->decimal('rate_per_hour', 8, 2)->nullable();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->unsignedBigInteger('department_id')->nullable()->index();
                $table->unsignedBigInteger('designation_id')->nullable()->index();
                $table->unsignedBigInteger('creator_id')->nullable()->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();

                $table->foreign('shift')->references('id')->on('shifts')->onDelete('set null');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
                $table->foreign('designation_id')->references('id')->on('designations')->onDelete('set null');
                $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};