<?php

namespace Workdo\OpticalAndEyeCareCenter\Database\Seeders;

use Workdo\OpticalAndEyeCareCenter\Models\EyeCareAppoinment;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;
use Illuminate\Database\Seeder;

class DemoEyeCareAppoinmentSeeder extends Seeder
{
    public function run($userId): void
    {
        if (EyeCareAppoinment::where('created_by', $userId)->exists()) {
            return;
        }

        $patients = EyePatient::where('created_by', $userId)->get();
        $doctors = OpticalDoctor::where('created_by', $userId)->get();

        if ($patients->isEmpty()) {
            return;
        }

        $statuses = [0, 1, 2, 3]; // 0:Scheduled, 1:Confirmed, 2:Completed, 3:Cancelled
        $types = [0, 1, 2]; // 0:Consultation, 1:Follow-up, 2:Emergency

        foreach ($patients->take(10) as $patient) {
            $doctor = $doctors->isNotEmpty() ? $doctors->random() : null;

            EyeCareAppoinment::create([
                'patient_id' => $patient->id,
                'doctor_name' => $doctor ? $doctor->user_id : null,
                'appointment_datetime' => now()->addDays(rand(1, 30))->setTime(rand(9, 17), [0, 30][rand(0, 1)]),
                'status' => $statuses[array_rand($statuses)],
                'appointment_type' => $types[array_rand($types)],
                'notes' => 'Regular eye examination',
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }
}
