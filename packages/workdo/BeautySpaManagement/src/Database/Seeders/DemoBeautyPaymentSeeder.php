<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyBookingPayment;
use Workdo\BeautySpaManagement\Models\BeautyBooking;
use Carbon\Carbon;

class DemoBeautyPaymentSeeder extends Seeder
{
    public function run($userId)
    {
        if (BeautyBookingPayment::where('created_by', $userId)->exists()) {
            return;
        }
        
        $bookings = BeautyBooking::where('created_by', $userId)
            ->where('payment_option', 'Offline')
            ->get();
            
        if ($bookings->isEmpty()) {
            return;
        }

        // Create exactly 5 payment records
        $paymentData = [
            [
                'description' => 'Cash payment received at spa',
                'reference_number' => 'SPA1001'
            ],
            [
                'description' => 'Bank transfer payment confirmed',
                'reference_number' => 'SPA1002'
            ],
            [
                'description' => 'Credit card payment processed',
                'reference_number' => 'SPA1003'
            ],
            [
                'description' => 'Payment received for beauty service',
                'reference_number' => 'SPA1004'
            ],
            [
                'description' => 'Cash payment at location',
                'reference_number' => 'SPA1005'
            ]
        ];

        foreach ($paymentData as $index => $data) {
            $booking = $bookings->get($index % $bookings->count());
            if ($booking) {
                BeautyBookingPayment::create([
                    'booking_id' => $booking->id,
                    'total_person' => $booking->person,
                    'service' => $booking->service,
                    'payment_amount' => $booking->price,
                    'description' => $data['description'],
                    'payment_date' => Carbon::parse($booking->date)->addDays(rand(-1, 2))->format('Y-m-d'),
                    'customer_name' => $booking->name,
                    'reference_number' => $data['reference_number'],
                    'creator_id' => $userId,
                    'created_by' => $userId,
                ]);
            }
        }
    }
}