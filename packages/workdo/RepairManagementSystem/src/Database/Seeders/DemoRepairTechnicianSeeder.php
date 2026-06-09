<?php

namespace Workdo\RepairManagementSystem\Database\Seeders;

use Workdo\RepairManagementSystem\Models\RepairTechnician;
use Illuminate\Database\Seeder;

class DemoRepairTechnicianSeeder extends Seeder
{
    public function run($userId): void
    {
        if (RepairTechnician::where('created_by', $userId)->exists()) {
            return; // Skip seeding if data already exists
        }

        if (!empty($userId)) {
            $countryCodes = ['+1', '+44', '+91', '+61', '+81', '+49', '+33', '+39', '+55', '+971', '+86', '+7', '+27', '+82', '+34'];
            
            $technicians = [
                ['name' => 'Marcus Rodriguez', 'email' => 'marcus.rodriguez@repairtech.com'],
                ['name' => 'Elena Petrov', 'email' => 'elena.petrov@techrepair.com'],
                ['name' => 'Ahmed Hassan', 'email' => 'ahmed.hassan@devicefix.com'],
                ['name' => 'Sarah Chen', 'email' => 'sarah.chen@mobilerestore.com'],
                ['name' => 'Viktor Kowalski', 'email' => 'viktor.kowalski@gadgetcare.com'],
                ['name' => 'Isabella Santos', 'email' => 'isabella.santos@quickfix.com'],
                ['name' => 'Dmitri Volkov', 'email' => 'dmitri.volkov@techsolutions.com'],
                ['name' => 'Priya Sharma', 'email' => 'priya.sharma@repairpro.com'],
                ['name' => 'Carlos Mendoza', 'email' => 'carlos.mendoza@devicedoctor.com'],
                ['name' => 'Yuki Tanaka', 'email' => 'yuki.tanaka@techmaster.com'],
                ['name' => 'Fatima Al-Zahra', 'email' => 'fatima.alzahra@smartrepair.com'],
                ['name' => 'Giovanni Rossi', 'email' => 'giovanni.rossi@fixitfast.com'],
                ['name' => 'Anastasia Popov', 'email' => 'anastasia.popov@repairzone.com'],
                ['name' => 'Raj Patel', 'email' => 'raj.patel@technicianpro.com'],
                ['name' => 'Lucia Fernandez', 'email' => 'lucia.fernandez@gadgetguru.com'],
                ['name' => 'Kenji Nakamura', 'email' => 'kenji.nakamura@repairstation.com'],
                ['name' => 'Amara Okafor', 'email' => 'amara.okafor@devicecare.com'],
                ['name' => 'Sebastian Mueller', 'email' => 'sebastian.mueller@techfix.com'],
                ['name' => 'Zara Khan', 'email' => 'zara.khan@mobilemend.com'],
                ['name' => 'Leonardo Silva', 'email' => 'leonardo.silva@repairexpert.com'],
                ['name' => 'Ingrid Larsson', 'email' => 'ingrid.larsson@techservice.com'],
                ['name' => 'Omar Al-Rashid', 'email' => 'omar.alrashid@quicktech.com'],
                ['name' => 'Mei-Lin Wang', 'email' => 'meilin.wang@devicespecialist.com'],
                ['name' => 'Nikolai Petersen', 'email' => 'nikolai.petersen@repairworks.com'],
                ['name' => 'Aisha Okonkwo', 'email' => 'aisha.okonkwo@techrepairs.com'],
                ['name' => 'Hiroshi Yamamoto', 'email' => 'hiroshi.yamamoto@gadgetfix.com'],
                ['name' => 'Valentina Rossi', 'email' => 'valentina.rossi@smarttech.com'],
                ['name' => 'Arjun Gupta', 'email' => 'arjun.gupta@repaircentral.com'],
                ['name' => 'Katarina Novak', 'email' => 'katarina.novak@techsupport.com'],
                ['name' => 'Hassan Al-Mahmoud', 'email' => 'hassan.almahmoud@devicerepair.com']
            ];

            foreach ($technicians as $technician) {
                RepairTechnician::create(array_merge($technician, [
                    'mobile_no' => $countryCodes[array_rand($countryCodes)] . mt_rand(1000000000, 9999999999),
                    'creator_id' => $userId,
                    'created_by' => $userId,
                ]));
            }
        }
    }
}