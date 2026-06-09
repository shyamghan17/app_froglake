<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyWorking;

class DemoBeautyWorkingSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyWorking::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            BeautyWorking::updateOrCreate(
                ['created_by' => $userId],
                [
                    'opening_time' => '09:00:00',
                    'closing_time' => '18:00:00',
                    'day_of_week' => 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                    'holiday_setting' => 'on',
                    'creator_id' => $userId,
                ]
            );
        }
    }
}