<?php

namespace Workdo\OpticalAndEyeCareCenter\Database\Seeders;

use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;
use Illuminate\Database\Seeder;

class DemoEyePatientSeeder extends Seeder
{
    public function run($userId): void
    {
        if (EyePatient::where('created_by', $userId)->exists()) {
            return;
        }

        $doctors = OpticalDoctor::where('created_by', $userId)->get();

        $patients = [
            ['name' => 'Alice Johnson', 'contact' => '+1234567890'],
            ['name' => 'Bob Williams', 'contact' => '+1234567891'],
            ['name' => 'Carol Martinez', 'contact' => '+1234567892'],
            ['name' => 'David Garcia', 'contact' => '+1234567893'],
            ['name' => 'Emma Rodriguez', 'contact' => '+1234567894'],
            ['name' => 'Frank Wilson', 'contact' => '+1234567895'],
            ['name' => 'Grace Lee', 'contact' => '+1234567896'],
            ['name' => 'Henry Taylor', 'contact' => '+1234567897'],
            ['name' => 'Ivy Anderson', 'contact' => '+1234567898'],
            ['name' => 'Jack Thomas', 'contact' => '+1234567899'],
        ];

        $genders = [0, 1, 2];

        foreach ($patients as $patientData) {
            EyePatient::create([
                'patient_name' => $patientData['name'],
                'dob' => now()->subYears(rand(20, 70))->subDays(rand(1, 365)),
                'gender' => $genders[array_rand($genders)],
                'contact_no' => $patientData['contact'],
                'address' => fake()->address(),
                'medical_history' => 'No significant medical history',
                'previous_prescriptions' => 'None',
                'preferred_doctor' => $doctors->isNotEmpty() ? $doctors->random()->user_id : null,
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }
}
