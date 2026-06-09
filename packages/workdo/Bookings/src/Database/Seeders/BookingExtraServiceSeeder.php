<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\Bookings\Models\BookingExtraService;
use App\Models\User;
use Carbon\Carbon;

class BookingExtraServiceSeeder extends Seeder
{
    public function run($userId)
    {
        if (!empty($userId)) {
            $this->createExtraServicesForUser($userId);
        }
    }
    
    private function createExtraServicesForUser($userId)
    {
        if (BookingExtraService::where('created_by', $userId)->exists()) {
            return;
        }
        
        $extraServices = [
            ['name' => 'Hair Wash & Conditioning', 'amount' => 15.00],
            ['name' => 'Scalp Massage', 'amount' => 20.00],
            ['name' => 'Hot Towel Treatment', 'amount' => 10.00],
            ['name' => 'Paraffin Hand Treatment', 'amount' => 25.00],
            ['name' => 'Foot Soak & Scrub', 'amount' => 30.00],
            ['name' => 'Aromatherapy Add-on', 'amount' => 18.00],
            ['name' => 'Eye Mask Treatment', 'amount' => 12.00],
            ['name' => 'Lip Scrub & Balm', 'amount' => 8.00],
            ['name' => 'Cuticle Oil Treatment', 'amount' => 10.00],
            ['name' => 'Hair Glossing', 'amount' => 35.00],
            ['name' => 'Beard Trim & Style', 'amount' => 15.00],
            ['name' => 'Eyebrow Tinting', 'amount' => 12.00],
            ['name' => 'Lash Extension Touch-up', 'amount' => 40.00],
            ['name' => 'Nail Art Design', 'amount' => 20.00],
            ['name' => 'Express Blowdry', 'amount' => 25.00]
        ];

        foreach ($extraServices as $service) {
            BookingExtraService::create([
                'name' => $service['name'],
                'amount' => $service['amount'],
                'status' => true,
                'created_by' => $userId,
                'creator_id' => $userId,
                'created_at' => Carbon::now()->subDays(rand(0, 180)),
            ]);
        }
    }
}