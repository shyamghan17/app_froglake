<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\Bookings\Models\BookingAppointment;
use Workdo\Bookings\Models\BookingCustomer;
use Workdo\Bookings\Models\BookingPackage;
use Workdo\Bookings\Models\BookingStaff;
use Workdo\ProductService\Models\ProductServiceItem;
use Carbon\Carbon;
use App\Models\User;

class BookingAppointmentSeeder extends Seeder
{
    public function run($userId)
    {        
        if (!empty($userId)) {
            $this->createAppointmentsForUser($userId);
        }
    }
    
    private function createAppointmentsForUser($userId)
    {
        if (BookingAppointment::where('created_by', $userId)->exists()) {
            return;
        }
        
        $items = ProductServiceItem::where('created_by', $userId)->where('type', 'bookings')->get();
        $packages = BookingPackage::where('created_by', $userId)->get();
        $staffUsers = User::whereIn('id', BookingStaff::where('created_by', $userId)->pluck('staff_id'))->get();
        $customers = BookingCustomer::where('created_by', $userId)->get();
        
        if ($items->isEmpty() || $packages->isEmpty() || $staffUsers->isEmpty() || $customers->isEmpty()) return;
        
        $currentYear = date('Y');
        // Get business hours for generating realistic appointment times
        $businessHours = \Workdo\Bookings\Models\BookingBusinessHours::getBusinessHours($userId);
        
        $appointmentData = [];
        $daysOffsets = [-7, -5, -3, -1, 0, 1, 2, 3, 5, 7, 10, 12, 14, 17, 21];
        $statuses = ['pending', 'confirmed', 'completed'];
        $paymentMethods = ['offline'];
        $paymentStatuses = ['pending', 'paid'];
        
        foreach ($daysOffsets as $index => $offset) {
            $date = Carbon::today()->addDays($offset);
            $dayName = strtolower($date->format('l'));
            $dayHours = $businessHours[$dayName] ?? null;
            
            if (!$dayHours || $dayHours['is_closed'] || empty($dayHours['time_slots'])) {
                // Skip closed days, use default time for Sunday
                $appointmentData[] = [
                    'days_offset' => $offset,
                    'time' => ['10:00', '11:00'],
                    'payment' => $paymentMethods[array_rand($paymentMethods)],
                    'status' => $offset < 0 ? 'completed' : $statuses[array_rand($statuses)],
                    'payment_status' => $offset < 0 ? 'paid' : $paymentStatuses[array_rand($paymentStatuses)]
                ];
                continue;
            }
            
            // Pick random time slot from available business hours
            $timeSlot = $dayHours['time_slots'][array_rand($dayHours['time_slots'])];
            $openTime = strtotime($timeSlot['open']);
            $closeTime = strtotime($timeSlot['close']);
            
            // Generate random appointment time within business hours (30-90 min duration)
            $duration = [30, 60, 90][array_rand([30, 60, 90])] * 60; // seconds
            $maxStartTime = $closeTime - $duration;
            $appointmentStart = $openTime + rand(0, max(0, $maxStartTime - $openTime));
            $appointmentEnd = $appointmentStart + $duration;
            
            $appointmentData[] = [
                'days_offset' => $offset,
                'time' => [date('H:i', $appointmentStart), date('H:i', $appointmentEnd)],
                'payment' => $paymentMethods[array_rand($paymentMethods)],
                'status' => $offset < 0 ? 'completed' : $statuses[array_rand($statuses)],
                'payment_status' => $offset < 0 ? 'paid' : $paymentStatuses[array_rand($paymentStatuses)]
            ];
        }
        
        foreach ($appointmentData as $index => $data) {
            $appointmentNumber = 'APT-' . $currentYear . '-' . $userId . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            
            $selectedPackage = $packages->random();
            $selectedItem = $items->find($selectedPackage->item_id) ?: $items->random();
            
            BookingAppointment::create([
                'appointment_number' => $appointmentNumber,
                'date' => Carbon::today()->addDays($data['days_offset'])->format('Y-m-d'),
                'item_id' => $selectedItem->id,
                'package_id' => $selectedPackage->id,
                'staff_id' => $staffUsers->random()->id,
                'customer_id' => $customers->random()->id,
                'start_time' => $data['time'][0],
                'end_time' => $data['time'][1],
                'payment' => $data['payment'],
                'payment_status' => $data['payment_status'],
                'status' => $data['status'],
                'created_by' => $userId,
                'creator_id' => $userId,
                'created_at' => Carbon::now()->subDays(rand(0, 180)),
            ]);
        }
    }
}