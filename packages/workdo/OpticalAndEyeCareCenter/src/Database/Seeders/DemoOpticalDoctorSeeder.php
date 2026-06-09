<?php

namespace Workdo\OpticalAndEyeCareCenter\Database\Seeders;

use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;
use Workdo\OpticalAndEyeCareCenter\Models\OpticalSpecialization;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoOpticalDoctorSeeder extends Seeder
{
    public function run($userId): void
    {
        if (OpticalDoctor::where('created_by', $userId)->exists()) {
            return;
        }

        $doctorRole = Role::where('name', 'doctor')->where('created_by', $userId)->where('guard_name', 'web')->first();

        if (!$doctorRole) {
            return;
        }

        $specializations = OpticalSpecialization::where('created_by', $userId)->get();

        $doctors = [
            ['name' => 'Dr. John Smith', 'email' => 'john.smith@optical.com', 'license' => 'OPT-2024-001', 'status' => 0,'mobile' => '+12125551001'],
            ['name' => 'Dr. Sarah Williams', 'email' => 'sarah.williams@optical.com', 'license' => 'OPT-2024-002', 'status' => 1,'mobile' => '+14155551002'],
            ['name' => 'Dr. Michael Brown', 'email' => 'michael.brown@optical.com', 'license' => 'OPT-2024-003', 'status' => 2, 'mobile' => '+13055551003'],
            ['name' => 'Dr. Emily Davis', 'email' => 'emily.davis@optical.com', 'license' => 'OPT-2024-004', 'status' => 3, 'mobile' => '+16175551004'],
            ['name' => 'Dr. Robert Johnson', 'email' => 'robert.johnson@optical.com', 'license' => 'OPT-2024-005', 'status' => 0,'mobile' => '+17185551005'],
            ['name' => 'Dr. Lisa Anderson', 'email' => 'lisa.anderson@optical.com', 'license' => 'OPT-2024-006', 'status' => 1,'mobile' => '+19175551006'],
            ['name' => 'Dr. James Wilson', 'email' => 'james.wilson@optical.com', 'license' => 'OPT-2024-007', 'status' => 2,'mobile' => '+13345551007'],
        ];

        $genders = [0, 1, 2];

        foreach ($doctors as $doctorData) {
            $user = User::create([
                'name' => $doctorData['name'],
                'email' => $doctorData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('1234'),
                'mobile_no' => $doctorData['mobile'],
                'type' => 'doctor',
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);

            $user->assignRole($doctorRole);

            OpticalDoctor::create([
                'user_id' => $user->id,
                'license_number' => $doctorData['license'],
                'gender' => $genders[array_rand($genders)],
                'years_of_experience' => rand(5, 25),
                'consultation_fee' => rand(50, 200),
                'qualifications' => 'MD, Ophthalmology',
                'status' => $doctorData['status'],
                'hospital_specialization_id' => $specializations->isNotEmpty() ? $specializations->random()->id : null,
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }
}
