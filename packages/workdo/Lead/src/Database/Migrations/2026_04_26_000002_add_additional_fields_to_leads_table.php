<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'category')) {
                $table->string('category')->nullable()->after('website');
            }
            if (!Schema::hasColumn('leads', 'address')) {
                $table->string('address')->nullable()->after('category');
            }
            if (!Schema::hasColumn('leads', 'district')) {
                $table->string('district')->nullable()->after('address');
            }
            if (!Schema::hasColumn('leads', 'province')) {
                $table->string('province')->nullable()->after('district');
            }
            if (!Schema::hasColumn('leads', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('province');
            }
            if (!Schema::hasColumn('leads', 'remarks')) {
                $table->longText('remarks')->nullable()->after('contact_person');
            }
            if (!Schema::hasColumn('leads', 'is_live')) {
                $table->boolean('is_live')->default(false)->after('remarks');
            }
            if (!Schema::hasColumn('leads', 'company_pan')) {
                $table->string('company_pan')->nullable()->after('is_live');
            }
            if (!Schema::hasColumn('leads', 'lead_status')) {
                $table->string('lead_status')->nullable()->after('company_pan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $columns = [
                'category',
                'address',
                'district',
                'province',
                'contact_person',
                'remarks',
                'is_live',
                'company_pan',
                'lead_status',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('leads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

