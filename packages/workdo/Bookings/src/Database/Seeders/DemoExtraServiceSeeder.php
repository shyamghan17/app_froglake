<?php

namespace Workdo\Bookings\Database\Seeders;

use Workdo\Bookings\Models\ExtraService;
use Illuminate\Database\Seeder;
use Workdo\Bookings\Models\BookingExtraService;
use Carbon\Carbon;



class DemoExtraServiceSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BookingExtraService::where('created_by', $userId)->exists()) {
            return;
        }

        $extraServices = [
            'Hair Wash & Conditioning',
            'Scalp Massage',
            'Hot Towel Treatment',
            'Paraffin Hand Treatment',
            'Foot Soak & Scrub',
            'Aromatherapy Add-on',
            'Eye Mask Treatment',
            'Lip Scrub & Balm',
            'Cuticle Oil Treatment',
            'Hair Glossing',
            'Beard Trim & Style',
            'Eyebrow Tinting',
            'Lash Extension Touch-up',
            'Nail Art Design',
            'Express Blowdry'
        ];

        foreach ($extraServices as $service) {
            BookingExtraService::create([
                'name' => $service,
                'status' => true,
                'created_by' => $userId,
                'creator_id' => $userId,
                'created_at' => Carbon::now()->subDays(rand(0, 180)),
            ]);
        }
    }
}
