<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyBooking;
use Workdo\BeautySpaManagement\Models\BeautyBookingReceipt;
use Workdo\BeautySpaManagement\Models\BeautyService;

class DemoBeautyBookingSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyBooking::where('created_by', $userId)->exists()) {
            return;
        }

        if (!empty($userId)) {
            $services = BeautyService::where('created_by', $userId)->get();

            if ($services->isEmpty()) {
                return;
            }

            $firstService = $services->first();

              // 1. Frontend Online Booking - Stripe Payment
            $service1 = BeautyService::where('created_by', $userId)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Sarah Johnson',
                'email'          => 'sarah.johnson@gmail.com',
                'service'        => $service1->id,
                'date'           => now()->addDays(1)->format('Y-m-d'),
                'start_time'     => '09:00:00',
                'end_time'       => '10:00:00',
                'person'         => 1,
                'price'          => $service1->price,
                'phone_number'   => '+16195550124',
                'gender'         => 'female',
                'reference'      => 'Google',
                'notes'          => 'First time client, has sensitive skin.',
                'payment_option' => 'Stripe',
                'payment_status' => 'paid',
                'stage_id'       => 2,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // 2. Frontend Online Booking - Paypal Payment
            $service2 = BeautyService::where('created_by', $userId)->skip(1)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Michael Chen',
                'email'          => 'michael.chen@yahoo.com',
                'service'        => $service2->id,
                'date'           => now()->format('Y-m-d'),
                'start_time'     => '14:30:00',
                'end_time'       => '16:00:00',
                'person'         => 1,
                'price'          => $service2->price,
                'phone_number'   => '+16195550125',
                'gender'         => 'male',
                'reference'      => 'Friend',
                'notes'          => 'Prefers medium pressure massage.',
                'payment_option' => 'Paypal',
                'payment_status' => 'paid',
                'stage_id'       => 2,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // 3. Frontend Offline Booking - Pay at Location
            $service3 = BeautyService::where('created_by', $userId)->skip(2)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Emma Rodriguez',
                'email'          => 'emma.rodriguez@hotmail.com',
                'service'        => $service3->id,
                'date'           => now()->addDays(2)->format('Y-m-d'),
                'start_time'     => '11:00:00',
                'end_time'       => '12:15:00',
                'person'         => 1,
                'price'          => $service3->price,
                'phone_number'   => '+16195550126',
                'gender'         => 'female',
                'reference'      => 'Social Media',
                'notes'          => 'Anniversary celebration.',
                'payment_option' => 'Offline',
                'payment_status' => 'pending',
                'stage_id'       => 1,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // 4. Frontend Free Booking - Promotional Service
            $service4 = BeautyService::where('created_by', $userId)->skip(3)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'David Thompson',
                'email'          => 'david.thompson@outlook.com',
                'service'        => $service4->id,
                'date'           => now()->addDays(3)->format('Y-m-d'),
                'start_time'     => '16:00:00',
                'end_time'       => '16:45:00',
                'person'         => 1,
                'price'          => $service4->price,
                'phone_number'   => '+16195550127',
                'gender'         => 'male',
                'reference'      => 'Google',
                'notes'          => 'Promotional consultation.',
                'payment_option' => 'Offline',
                'payment_status' => 'paid',
                'stage_id'       => 2,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // 5. Backend Manual Entry - Admin Created
            $service5 = BeautyService::where('created_by', $userId)->skip(4)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Jessica Martinez',
                'email'          => 'jessica.martinez@gmail.com',
                'service'        => $service5->id,
                'date'           => now()->addDays(4)->format('Y-m-d'),
                'start_time'     => '10:30:00',
                'end_time'       => '12:00:00',
                'person'         => 1,
                'price'          => $service5->price,
                'phone_number'   => '+16195550128',
                'gender'         => 'female',
                'reference'      => 'Friend',
                'notes'          => 'Regular client, neutral colors.',
                'payment_option' => 'Offline',
                'payment_status' => 'pending',
                'stage_id'       => 1,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // 6. Frontend Online Booking - Stripe Payment
            $service6 = BeautyService::where('created_by', $userId)->skip(5)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Amanda Wilson',
                'email'          => 'amanda.wilson@yahoo.com',
                'service'        => $service6->id,
                'date'           => now()->addDays(5)->format('Y-m-d'),
                'start_time'     => '08:00:00',
                'end_time'       => '10:00:00',
                'person'         => 1,
                'price'          => $service6->price,
                'phone_number'   => '+16195550129',
                'gender'         => 'female',
                'reference'      => 'Social Media',
                'notes'          => 'Wedding day makeup.',
                'payment_option' => 'Stripe',
                'payment_status' => 'paid',
                'stage_id'       => 2,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);
              // 13. Frontend Online Booking - PayPal Payment
            $service13 = BeautyService::where('created_by', $userId)->skip(12)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Daniel Wilson',
                'email'          => 'daniel.wilson@hotmail.com',
                'service'        => $service13->id,
                'date'           => now()->addDays(12)->format('Y-m-d'),
                'start_time'     => '16:30:00',
                'end_time'       => '17:45:00',
                'person'         => 1,
                'price'          => $service13->price,
                'phone_number'   => '+16195550136',
                'gender'         => 'male',
                'reference'      => 'Social Media',
                'notes'          => 'Beard grooming service.',
                'payment_option' => 'Offline',
                'payment_status' => 'pending',
                'stage_id'       => 2,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // 7. Frontend Offline Booking - Pay at Location
            $service7 = BeautyService::where('created_by', $userId)->skip(6)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Robert Garcia',
                'email'          => 'robert.garcia@gmail.com',
                'service'        => $service7->id,
                'date'           => now()->addDays(6)->format('Y-m-d'),
                'start_time'     => '13:00:00',
                'end_time'       => '14:00:00',
                'person'         => 1,
                'price'          => $service7->price,
                'phone_number'   => '+16195550130',
                'gender'         => 'male',
                'reference'      => 'Other',
                'notes'          => 'Anti-aging treatment.',
                'payment_option' => 'Offline',
                'payment_status' => 'pending',
                'stage_id'       => 1,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // 8. Backend Manual Entry - Admin Created
            $service8 = BeautyService::where('created_by', $userId)->skip(7)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Lisa Anderson',
                'email'          => 'lisa.anderson@hotmail.com',
                'service'        => $service8->id,
                'date'           => now()->addDays(7)->format('Y-m-d'),
                'start_time'     => '15:30:00',
                'end_time'       => '16:15:00',
                'person'         => 1,
                'price'          => $service8->price,
                'phone_number'   => '+16195550131',
                'gender'         => 'female',
                'reference'      => 'Google',
                'notes'          => 'Chemical peel treatment.',
                'payment_option' => 'Offline',
                'payment_status' => 'pending',
                'stage_id'       => 0,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // 9. Frontend Online Booking - Paypal Payment (Couples)
            $service9 = BeautyService::where('created_by', $userId)->skip(8)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Mark Johnson',
                'email'          => 'mark.johnson@outlook.com',
                'service'        => $service9->id,
                'date'           => now()->addDays(8)->format('Y-m-d'),
                'start_time'     => '10:00:00',
                'end_time'       => '12:00:00',
                'person'         => 2,
                'price'          => $service9->price * 2,
                'phone_number'   => '+16195550132',
                'gender'         => 'male',
                'reference'      => 'Friend',
                'notes'          => 'Couples package.',
                'payment_option' => 'Paypal',
                'payment_status' => 'paid',
                'stage_id'       => 2,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);
              // 14. Frontend Offline Booking - Pay at Location
            $service14 = BeautyService::where('created_by', $userId)->skip(13)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Nicole Taylor',
                'email'          => 'nicole.taylor@outlook.com',
                'service'        => $service14->id,
                'date'           => now()->addDays(13)->format('Y-m-d'),
                'start_time'     => '10:00:00',
                'end_time'       => '11:15:00',
                'person'         => 1,
                'price'          => $service14->price,
                'phone_number'   => '+16195550137',
                'gender'         => 'female',
                'reference'      => 'Other',
                'notes'          => 'Manicure and pedicure.',
                'payment_option' => 'Offline',
                'payment_status' => 'pending',
                'stage_id'       => 0,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // 10. Frontend Offline Booking - Pay at Location
            $service10 = BeautyService::where('created_by', $userId)->skip(9)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Jennifer Davis',
                'email'          => 'jennifer.davis@gmail.com',
                'service'        => $service10->id,
                'date'           => now()->addDays(9)->format('Y-m-d'),
                'start_time'     => '09:00:00',
                'end_time'       => '13:00:00',
                'person'         => 1,
                'price'          => $service10->price,
                'phone_number'   => '+16195550133',
                'gender'         => 'female',
                'reference'      => 'Social Media',
                'notes'          => 'Bridal package.',
                'payment_option' => 'Offline',
                'payment_status' => 'pending',
                'stage_id'       => 0,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // 11. Frontend Online Booking - Stripe Payment
            $service11 = BeautyService::where('created_by', $userId)->skip(10)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Kevin Brown',
                'email'          => 'kevin.brown@gmail.com',
                'service'        => $service11->id,
                'date'           => now()->addDays(10)->format('Y-m-d'),
                'start_time'     => '11:00:00',
                'end_time'       => '12:30:00',
                'person'         => 1,
                'price'          => $service11->price,
                'phone_number'   => '+16195550134',
                'gender'         => 'male',
                'reference'      => 'Google',
                'notes'          => 'Hair styling for event.',
                'payment_option' => 'Offline',
                'payment_status' => 'pending',
                'stage_id'       => 2,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // 12. Frontend Offline Booking - Pay at Location
            $service12 = BeautyService::where('created_by', $userId)->skip(11)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Rachel Green',
                'email'          => 'rachel.green@yahoo.com',
                'service'        => $service12->id,
                'date'           => now()->addDays(11)->format('Y-m-d'),
                'start_time'     => '14:00:00',
                'end_time'       => '15:30:00',
                'person'         => 1,
                'price'          => $service12->price,
                'phone_number'   => '+16195550135',
                'gender'         => 'female',
                'reference'      => 'Friend',
                'notes'          => 'Facial treatment.',
                'payment_option' => 'Offline',
                'payment_status' => 'pending',
                'stage_id'       => 1,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);



              // 15. Frontend Online Booking - Stripe Payment
            $service15 = BeautyService::where('created_by', $userId)->skip(14)->first() ?? $firstService;
            BeautyBooking::create([
                'name'           => 'Steven Martinez',
                'email'          => 'steven.martinez@gmail.com',
                'service'        => $service15->id,
                'date'           => now()->addDays(14)->format('Y-m-d'),
                'start_time'     => '13:30:00',
                'end_time'       => '15:00:00',
                'person'         => 1,
                'price'          => $service15->price,
                'phone_number'   => '+16195550138',
                'gender'         => 'male',
                'reference'      => 'Google',
                'notes'          => 'Skin care treatment.',
                'payment_option' => 'Stripe',
                'payment_status' => 'paid',
                'stage_id'       => 2,
                'creator_id'     => $userId,
                'created_by'     => $userId,
            ]);

              // Create receipts for completed bookings (stage_id = 2)
            $completedBookings = BeautyBooking::where('created_by', $userId)
                ->where('stage_id', 2)
                ->get();

            foreach ($completedBookings as $booking) {
                BeautyBookingReceipt::create([
                    'beauty_booking_id' => $booking->id,
                    'name'              => $booking->name,
                    'service'           => $booking->service,
                    'number'            => $booking->phone_number,
                    'gender'            => $booking->gender,
                    'start_time'        => $booking->start_time,
                    'end_time'          => $booking->end_time,
                    'price'             => $booking->price,
                    'payment_type'      => $booking->payment_option,
                    'creator_id'        => $userId,
                    'created_by'        => $userId,
                ]);
            }
        }
    }
}
