<?php

namespace Workdo\BulkSMS\Database\Seeders;

use Workdo\BulkSMS\Models\SingleSms;
use Illuminate\Database\Seeder;
use Workdo\BulkSMS\Models\BulkSmsContact;


class DemoSingleSmsSeeder extends Seeder
{
    public function run($userId): void
    {
         if (SingleSms::where('created_by', $userId)->exists()) {
            return; // Skip seeding if data already exists
        }
        $messages = [
            'Your appointment is confirmed for tomorrow at 2 PM.',
            'Thank you for your purchase. Your order will be delivered soon.',
            'Reminder: Your subscription expires in 3 days.',
            'Welcome to our service! Your account has been activated.',
            'Your payment of $50 has been received successfully.',
            'Meeting scheduled for Friday 10 AM in conference room.',
            'Your verification code is 123456. Valid for 10 minutes.',
            'Special offer: 20% discount on all items this weekend.',
            'Your flight booking is confirmed. Check-in opens 24 hours before.',
            'Password reset requested. Click link to reset your password.',
            'Delivery update: Your package is out for delivery.',
            'Event reminder: Annual conference starts tomorrow.',
            'Your loan application has been approved.',
            'System maintenance scheduled for tonight 11 PM - 2 AM.',
            'Happy birthday! Enjoy 15% off your next purchase.'
        ];

        $contacts = BulkSmsContact::where('created_by', $userId)->get();
        
        for ($i = 0; $i < 15; $i++) {
            $contact = $contacts->get($i % $contacts->count());
            
            SingleSms::create([
                'contact_id' => $contact->id,
                'mobile_no' => $contact->mobile_no,
                'status' => $i % 2 == 0 ? 'delivered' : 'failed',
                'sms' => $messages[$i],
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }
}