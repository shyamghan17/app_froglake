<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyGiftCard;
use Carbon\Carbon;

class DemoBeautyGiftCardSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyGiftCard::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            // 25 realistic gift card records with various statuses and balances
            $giftCardRecords = [
                ['card_code' => 'GC001250', 'customer' => 'Sarah Mitchell', 'balance' => 250.00, 'expiry_date' => Carbon::now()->addMonths(12), 'status' => true],
                ['card_code' => 'GC001500', 'customer' => 'Emily Rodriguez', 'balance' => 150.00, 'expiry_date' => Carbon::now()->addMonths(10), 'status' => true],
                ['card_code' => 'GC001100', 'customer' => 'Jessica Thompson', 'balance' => 100.00, 'expiry_date' => Carbon::now()->addMonths(8), 'status' => true],
                ['card_code' => 'GC001750', 'customer' => 'Amanda Wilson', 'balance' => 75.00, 'expiry_date' => Carbon::now()->addMonths(6), 'status' => true],
                ['card_code' => 'GC001300', 'customer' => 'Michelle Davis', 'balance' => 300.00, 'expiry_date' => Carbon::now()->addMonths(15), 'status' => true],
                ['card_code' => 'GC001050', 'customer' => 'Lisa Garcia', 'balance' => 50.00, 'expiry_date' => Carbon::now()->addMonths(4), 'status' => true],
                ['card_code' => 'GC001200', 'customer' => 'Rachel Brown', 'balance' => 200.00, 'expiry_date' => Carbon::now()->addMonths(9), 'status' => true],
                ['card_code' => 'GC001400', 'customer' => 'Jennifer Martinez', 'balance' => 0.00, 'expiry_date' => Carbon::now()->subMonths(2), 'status' => false],
                ['card_code' => 'GC001125', 'customer' => 'Ashley Lee', 'balance' => 125.00, 'expiry_date' => Carbon::now()->addMonths(7), 'status' => true],
                ['card_code' => 'GC001350', 'customer' => 'Nicole Anderson', 'balance' => 350.00, 'expiry_date' => Carbon::now()->addMonths(18), 'status' => true],
                ['card_code' => 'GC001080', 'customer' => 'Stephanie Taylor', 'balance' => 80.00, 'expiry_date' => Carbon::now()->addMonths(5), 'status' => true],
                ['card_code' => 'GC001225', 'customer' => 'Megan White', 'balance' => 225.00, 'expiry_date' => Carbon::now()->addMonths(11), 'status' => true],
                ['card_code' => 'GC001175', 'customer' => 'Kimberly Harris', 'balance' => 175.00, 'expiry_date' => Carbon::now()->addMonths(8), 'status' => true],
                ['card_code' => 'GC001000', 'customer' => 'Danielle Clark', 'balance' => 0.00, 'expiry_date' => Carbon::now()->subMonths(1), 'status' => false],
                ['card_code' => 'GC001450', 'customer' => 'Brittany Lewis', 'balance' => 450.00, 'expiry_date' => Carbon::now()->addMonths(20), 'status' => true],
                ['card_code' => 'GC001090', 'customer' => 'Samantha Walker', 'balance' => 90.00, 'expiry_date' => Carbon::now()->addMonths(6), 'status' => true],
                ['card_code' => 'GC001275', 'customer' => 'Christina Hall', 'balance' => 275.00, 'expiry_date' => Carbon::now()->addMonths(13), 'status' => true],
                ['card_code' => 'GC001150', 'customer' => 'Rebecca Allen', 'balance' => 150.00, 'expiry_date' => Carbon::now()->addMonths(9), 'status' => true],
                ['card_code' => 'GC001325', 'customer' => 'Laura Young', 'balance' => 325.00, 'expiry_date' => Carbon::now()->addMonths(16), 'status' => true],
                ['card_code' => 'GC001060', 'customer' => 'Melissa King', 'balance' => 60.00, 'expiry_date' => Carbon::now()->addMonths(3), 'status' => true],
                ['card_code' => 'GC001500B', 'customer' => 'Victoria Adams', 'balance' => 500.00, 'expiry_date' => Carbon::now()->addMonths(24), 'status' => true],
                ['card_code' => 'GC001110', 'customer' => 'Diana Scott', 'balance' => 110.00, 'expiry_date' => Carbon::now()->addMonths(7), 'status' => true],
                ['card_code' => 'GC001250B', 'customer' => 'Patricia Green', 'balance' => 0.00, 'expiry_date' => Carbon::now()->subMonths(3), 'status' => false],
                ['card_code' => 'GC001180', 'customer' => 'Karen Baker', 'balance' => 180.00, 'expiry_date' => Carbon::now()->addMonths(10), 'status' => true],
                ['card_code' => 'GC001400B', 'customer' => 'Susan Nelson', 'balance' => 400.00, 'expiry_date' => Carbon::now()->addMonths(22), 'status' => true]
            ];

            foreach ($giftCardRecords as $record) {
                BeautyGiftCard::create([
                    'card_code' => $record['card_code'],
                    'customer' => $record['customer'],
                    'balance' => $record['balance'],
                    'expiry_date' => $record['expiry_date']->toDateString(),
                    'status' => $record['status'],
                    'creator_id' => $userId,
                    'created_by' => $userId,
                ]);
            }
        }
    }
}