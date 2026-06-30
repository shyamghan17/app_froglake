<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('eye_test_prescriptions')) {
            return;
        }

        Schema::table('eye_test_prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('eye_test_prescriptions', 'clinical_schema_version')) {
                $table->unsignedTinyInteger('clinical_schema_version')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'follow_up_date')) {
                $table->date('follow_up_date')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'complaints')) {
                $table->json('complaints')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'visual_acuity')) {
                $table->json('visual_acuity')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'refraction')) {
                $table->json('refraction')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'eye_examination')) {
                $table->json('eye_examination')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'intraocular_pressure')) {
                $table->json('intraocular_pressure')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'medical_history')) {
                $table->json('medical_history')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'diagnosis')) {
                $table->json('diagnosis')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'medicines')) {
                $table->json('medicines')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'glasses_prescription')) {
                $table->json('glasses_prescription')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'eye_diagram')) {
                $table->json('eye_diagram')->nullable();
            }

            if (!Schema::hasColumn('eye_test_prescriptions', 'examiner_details')) {
                $table->json('examiner_details')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('eye_test_prescriptions')) {
            return;
        }

        Schema::table('eye_test_prescriptions', function (Blueprint $table) {
            $columns = [
                'clinical_schema_version',
                'follow_up_date',
                'complaints',
                'visual_acuity',
                'refraction',
                'eye_examination',
                'intraocular_pressure',
                'medical_history',
                'diagnosis',
                'medicines',
                'glasses_prescription',
                'eye_diagram',
                'examiner_details',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('eye_test_prescriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
