<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointment;
use Workdo\PhotoStudioManagement\Models\PhotoStudioService;
use Workdo\PhotoStudioManagement\Models\PhotoStudioTeamMember;

class DemoPhotoStudioAppointmentSeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioAppointment::where('created_by', $userId)->exists()) {
            return;
        }

        $serviceIds = PhotoStudioService::where('created_by', $userId)
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        $teamMemberIds = PhotoStudioTeamMember::where('created_by', $userId)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        if (empty($serviceIds)) {
            return;
        }

        $appointments = [
            [
                'name'               => 'Emma Johnson',
                'email'              => 'emma.johnson@example.com',
                'mobile_no'          => '+1234567890',
                'booking_start_date' => '2025-06-01 10:00:00',
                'booking_end_date'   => '2025-06-01 12:00:00',
                'status'             => 'pending',
                'payment_status'     => 'pending',
            ],
            [
                'name'               => 'Michael Smith',
                'email'              => 'michael.smith@example.com',
                'mobile_no'          => '+1987654321',
                'booking_start_date' => '2025-06-05 09:00:00',
                'booking_end_date'   => '2025-06-05 11:00:00',
                'status'             => 'scheduled',
                'payment_status'     => 'pending',
            ],
            [
                'name'               => 'Sarah Davis',
                'email'              => 'sarah.davis@example.com',
                'mobile_no'          => '+1122334455',
                'booking_start_date' => '2025-06-10 14:00:00',
                'booking_end_date'   => '2025-06-10 16:00:00',
                'status'             => 'completed',
                'payment_status'     => 'pending',
            ],
            [
                'name'               => 'David Wilson',
                'email'              => 'david.wilson@example.com',
                'mobile_no'          => '+1555666777',
                'booking_start_date' => '2025-06-12 11:00:00',
                'booking_end_date'   => '2025-06-12 13:00:00',
                'status'             => 'cancelled',
                'payment_status'     => 'pending',
            ],
            [
                'name'               => 'Jessica Brown',
                'email'              => 'jessica.brown@example.com',
                'mobile_no'          => '+1999888777',
                'booking_start_date' => '2025-06-15 10:00:00',
                'booking_end_date'   => '2025-06-15 12:30:00',
                'status'             => 'scheduled',
                'payment_status'     => 'pending',
            ],
            [
                'name'               => 'Robert Miller',
                'email'              => 'robert.miller@example.com',
                'mobile_no'          => '+1444555666',
                'booking_start_date' => '2025-06-18 09:00:00',
                'booking_end_date'   => '2025-06-18 11:00:00',
                'status'             => 'pending',
                'payment_status'     => 'pending',
            ],
            [
                'name'               => 'Amanda Garcia',
                'email'              => 'amanda.garcia@example.com',
                'mobile_no'          => '+1777888999',
                'booking_start_date' => '2025-06-20 13:00:00',
                'booking_end_date'   => '2025-06-20 15:00:00',
                'status'             => 'completed',
                'payment_status'     => 'pending',
            ],
            [
                'name'               => 'Christopher Martinez',
                'email'              => 'christopher.martinez@example.com',
                'mobile_no'          => '+1333444555',
                'booking_start_date' => '2025-06-22 10:00:00',
                'booking_end_date'   => '2025-06-22 14:00:00',
                'status'             => 'scheduled',
                'payment_status'     => 'pending',
            ],
            [
                'name'               => 'Lisa Anderson',
                'email'              => 'lisa.anderson@example.com',
                'mobile_no'          => '+1666777888',
                'booking_start_date' => '2025-06-25 09:00:00',
                'booking_end_date'   => '2025-06-25 11:00:00',
                'status'             => 'cancelled',
                'payment_status'     => 'pending',
            ],
            [
                'name'               => 'James Taylor',
                'email'              => 'james.taylor@example.com',
                'mobile_no'          => '+1222333444',
                'booking_start_date' => '2025-07-01 10:00:00',
                'booking_end_date'   => '2025-07-01 12:00:00',
                'status'             => 'pending',
                'payment_status'     => 'pending',
            ],
            [
                'name'               => 'Maria Rodriguez',
                'email'              => 'maria.rodriguez@example.com',
                'mobile_no'          => '+1111222333',
                'booking_start_date' => '2025-07-05 14:00:00',
                'booking_end_date'   => '2025-07-05 16:00:00',
                'status'             => 'completed',
                'payment_status'     => 'pending',
            ],
            [
                'name'               => 'William Lee',
                'email'              => 'william.lee@example.com',
                'mobile_no'          => '+1444333222',
                'booking_start_date' => '2025-07-08 11:00:00',
                'booking_end_date'   => '2025-07-08 13:00:00',
                'status'             => 'scheduled',
                'payment_status'     => 'pending',
            ],
        ];

        foreach ($appointments as $data) {
            $service = PhotoStudioService::find($serviceIds[array_rand($serviceIds)]);

            PhotoStudioAppointment::create([
                'name'               => $data['name'],
                'email'              => $data['email'],
                'mobile_no'          => $data['mobile_no'],
                'team_member_ids'    => !empty($teamMemberIds) ? array_map('strval', array_slice($teamMemberIds, 0, rand(1, min(3, count($teamMemberIds))))) : [],
                'booking_start_date' => $data['booking_start_date'],
                'booking_end_date'   => $data['booking_end_date'],
                'service_id'         => $service->id,
                'price'              => $service->price,
                'status'             => $data['status'],
                'payment_status'     => $data['payment_status'],
                'creator_id'         => $userId,
                'created_by'         => $userId,
            ]);
        }
    }
}
