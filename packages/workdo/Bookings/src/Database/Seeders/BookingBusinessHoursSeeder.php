<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\Bookings\Models\BookingBusinessHours;

class BookingBusinessHoursSeeder extends Seeder
{
    public function run($userId)
    {
        if (!empty($userId)) {
            $defaultHours = BookingBusinessHours::getDefaultHours();
            
            foreach ($defaultHours as $day => $hours) {
                BookingBusinessHours::updateOrCreate(
                    [
                        'day_of_week' => $day,
                        'created_by' => $userId // Default company
                    ],
                    [
                        'is_closed' => $hours['is_closed'],
                        'time_slots' => $hours['time_slots'],
                        'creator_id' => $userId
                    ]
                );
            }
        }
    }
}