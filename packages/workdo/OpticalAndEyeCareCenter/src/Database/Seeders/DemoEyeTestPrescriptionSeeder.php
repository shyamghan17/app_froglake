<?php

namespace Workdo\OpticalAndEyeCareCenter\Database\Seeders;

use Workdo\OpticalAndEyeCareCenter\Models\EyeTestPrescription;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;
use Illuminate\Database\Seeder;

class DemoEyeTestPrescriptionSeeder extends Seeder
{
    public function run($userId): void
    {
        if (EyeTestPrescription::where('created_by', $userId)->exists()) {
            return;
        }

        $patients = EyePatient::where('created_by', $userId)->get();
        $doctors = OpticalDoctor::where('created_by', $userId)->get();

        if ($patients->isEmpty()) {
            return;
        }

        foreach ($patients->take(10) as $index => $patient) {
            $testDate = now()->subDays(rand(1, 90));
            $doctor = $doctors->isNotEmpty() ? $doctors->random() : null;

            // Create some expired and some valid prescriptions
            $expiryDate = $index < 3 
                ? $testDate->copy()->subDays(rand(1, 30))  // Expired
                : $testDate->copy()->addYear();             // Valid

            EyeTestPrescription::create([
                'patient_id' => $patient->id,
                'doctor_name' => $doctor ? $doctor->user_id : null,
                'test_date' => $testDate,
                'test_results' => 'Vision: 20/40, Myopia detected',
                'prescription_details' => 'SPH: -2.00, CYL: -0.50, AXIS: 180',
                'prescription_expiry_date' => $expiryDate,
                'notes' => 'Regular checkup recommended in 6 months',
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }
}
