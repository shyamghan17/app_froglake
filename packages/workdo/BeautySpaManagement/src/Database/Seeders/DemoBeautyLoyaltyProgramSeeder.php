<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyLoyaltyProgram;
use Carbon\Carbon;

class DemoBeautyLoyaltyProgramSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyLoyaltyProgram::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            // 20 realistic loyalty program records ordered oldest to newest (6 months)
            $loyaltyRecords = [
                ['customer_name' => 'Sarah Johnson', 'points_earned' => 1250, 'points_redeemed' => 500, 'date' => Carbon::now()->subDays(180)],
                ['customer_name' => 'Emily Davis', 'points_earned' => 890, 'points_redeemed' => 200, 'date' => Carbon::now()->subDays(171)],
                ['customer_name' => 'Jessica Wilson', 'points_earned' => 2100, 'points_redeemed' => 800, 'date' => Carbon::now()->subDays(162)],
                ['customer_name' => 'Amanda Brown', 'points_earned' => 675, 'points_redeemed' => 150, 'date' => Carbon::now()->subDays(153)],
                ['customer_name' => 'Michelle Garcia', 'points_earned' => 1450, 'points_redeemed' => 600, 'date' => Carbon::now()->subDays(144)],
                ['customer_name' => 'Lisa Martinez', 'points_earned' => 980, 'points_redeemed' => 300, 'date' => Carbon::now()->subDays(135)],
                ['customer_name' => 'Rachel Thompson', 'points_earned' => 1800, 'points_redeemed' => 750, 'date' => Carbon::now()->subDays(126)],
                ['customer_name' => 'Jennifer Lee', 'points_earned' => 720, 'points_redeemed' => 100, 'date' => Carbon::now()->subDays(117)],
                ['customer_name' => 'Ashley Rodriguez', 'points_earned' => 1320, 'points_redeemed' => 450, 'date' => Carbon::now()->subDays(108)],
                ['customer_name' => 'Nicole Anderson', 'points_earned' => 560, 'points_redeemed' => 0, 'date' => Carbon::now()->subDays(99)],
                ['customer_name' => 'Stephanie Taylor', 'points_earned' => 2250, 'points_redeemed' => 900, 'date' => Carbon::now()->subDays(90)],
                ['customer_name' => 'Megan White', 'points_earned' => 840, 'points_redeemed' => 250, 'date' => Carbon::now()->subDays(81)],
                ['customer_name' => 'Kimberly Harris', 'points_earned' => 1150, 'points_redeemed' => 400, 'date' => Carbon::now()->subDays(72)],
                ['customer_name' => 'Danielle Clark', 'points_earned' => 690, 'points_redeemed' => 150, 'date' => Carbon::now()->subDays(63)],
                ['customer_name' => 'Brittany Lewis', 'points_earned' => 1680, 'points_redeemed' => 650, 'date' => Carbon::now()->subDays(54)],
                ['customer_name' => 'Samantha Walker', 'points_earned' => 920, 'points_redeemed' => 300, 'date' => Carbon::now()->subDays(45)],
                ['customer_name' => 'Christina Hall', 'points_earned' => 1420, 'points_redeemed' => 500, 'date' => Carbon::now()->subDays(36)],
                ['customer_name' => 'Rebecca Allen', 'points_earned' => 780, 'points_redeemed' => 200, 'date' => Carbon::now()->subDays(27)],
                ['customer_name' => 'Laura Young', 'points_earned' => 1950, 'points_redeemed' => 800, 'date' => Carbon::now()->subDays(18)],
                ['customer_name' => 'Melissa King', 'points_earned' => 650, 'points_redeemed' => 100, 'date' => Carbon::now()->subDays(9)]
            ];

            foreach ($loyaltyRecords as $record) {
                BeautyLoyaltyProgram::create([
                    'customer_name' => $record['customer_name'],
                    'points_earned' => $record['points_earned'],
                    'points_redeemed' => $record['points_redeemed'],
                    'last_updated' => $record['date']->toDateString(),
                    'creator_id' => $userId,
                    'created_by' => $userId,
                    'created_at' => $record['date'],
                    'updated_at' => $record['date'],
                ]);
            }
        }
    }
}