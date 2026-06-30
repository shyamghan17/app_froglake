<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('leads')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'designation')) {
                $table->string('designation')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('leads', 'company_name')) {
                $table->string('company_name')->nullable()->after('designation');
            }

            if (!Schema::hasColumn('leads', 'pan_vat_number')) {
                $table->string('pan_vat_number')->nullable()->after('company_name');
            }

            if (!Schema::hasColumn('leads', 'organization_type')) {
                $table->string('organization_type')->nullable()->after('pan_vat_number');
            }

            if (!Schema::hasColumn('leads', 'whatsapp_same_as_phone')) {
                $table->boolean('whatsapp_same_as_phone')->default(true)->after('organization_type');
            }

            if (!Schema::hasColumn('leads', 'whatsapp_viber_number')) {
                $table->string('whatsapp_viber_number', 30)->nullable()->after('whatsapp_same_as_phone');
            }

            if (!Schema::hasColumn('leads', 'address_line_1')) {
                $table->string('address_line_1')->nullable()->after('whatsapp_viber_number');
            }

            if (!Schema::hasColumn('leads', 'address_line_2')) {
                $table->string('address_line_2')->nullable()->after('address_line_1');
            }

            if (!Schema::hasColumn('leads', 'city')) {
                $table->string('city')->nullable()->after('address_line_2');
            }

            if (!Schema::hasColumn('leads', 'state')) {
                $table->string('state')->nullable()->after('city');
            }

            if (!Schema::hasColumn('leads', 'country')) {
                $table->string('country')->nullable()->after('state');
            }

            if (!Schema::hasColumn('leads', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('country');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('leads')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            $columns = [
                'designation',
                'company_name',
                'pan_vat_number',
                'organization_type',
                'whatsapp_same_as_phone',
                'whatsapp_viber_number',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'country',
                'postal_code',
            ];

            $existingColumns = array_values(array_filter(
                $columns,
                fn(string $column): bool => Schema::hasColumn('leads', $column)
            ));

            if ($existingColumns !== []) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
