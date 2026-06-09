<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautySubscriber;
use Carbon\Carbon;

class DemoBeautySubscriberSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautySubscriber::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            // 20 realistic subscriber emails ordered oldest to newest (6 months)
            $subscriberRecords = [
                ['email' => 'sarah.johnson@gmail.com', 'date' => Carbon::now()->subDays(180)],
                ['email' => 'emily.davis@yahoo.com', 'date' => Carbon::now()->subDays(171)],
                ['email' => 'jessica.wilson@hotmail.com', 'date' => Carbon::now()->subDays(162)],
                ['email' => 'amanda.brown@outlook.com', 'date' => Carbon::now()->subDays(153)],
                ['email' => 'michelle.garcia@gmail.com', 'date' => Carbon::now()->subDays(144)],
                ['email' => 'lisa.martinez@yahoo.com', 'date' => Carbon::now()->subDays(135)],
                ['email' => 'rachel.thompson@gmail.com', 'date' => Carbon::now()->subDays(126)],
                ['email' => 'jennifer.lee@hotmail.com', 'date' => Carbon::now()->subDays(117)],
                ['email' => 'ashley.rodriguez@outlook.com', 'date' => Carbon::now()->subDays(108)],
                ['email' => 'nicole.anderson@gmail.com', 'date' => Carbon::now()->subDays(99)],
                ['email' => 'stephanie.taylor@yahoo.com', 'date' => Carbon::now()->subDays(90)],
                ['email' => 'megan.white@gmail.com', 'date' => Carbon::now()->subDays(81)],
                ['email' => 'kimberly.harris@hotmail.com', 'date' => Carbon::now()->subDays(72)],
                ['email' => 'danielle.clark@outlook.com', 'date' => Carbon::now()->subDays(63)],
                ['email' => 'brittany.lewis@gmail.com', 'date' => Carbon::now()->subDays(54)],
                ['email' => 'samantha.walker@yahoo.com', 'date' => Carbon::now()->subDays(45)],
                ['email' => 'christina.hall@gmail.com', 'date' => Carbon::now()->subDays(36)],
                ['email' => 'rebecca.allen@hotmail.com', 'date' => Carbon::now()->subDays(27)],
                ['email' => 'laura.young@outlook.com', 'date' => Carbon::now()->subDays(18)],
                ['email' => 'melissa.king@gmail.com', 'date' => Carbon::now()->subDays(9)]
            ];

            foreach ($subscriberRecords as $record) {
                BeautySubscriber::create([
                    'email' => $record['email'],
                    'created_by' => $userId,
                    'created_at' => $record['date'],
                    'updated_at' => $record['date'],
                ]);
            }
        }
    }
}