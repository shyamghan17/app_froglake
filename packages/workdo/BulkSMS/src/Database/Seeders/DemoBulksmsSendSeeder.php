<?php

namespace Workdo\BulkSMS\Database\Seeders;

use Workdo\BulkSMS\Models\BulksmsSend;
use Workdo\BulkSMS\Models\BulksmsSendMessage;
use Workdo\BulkSMS\Models\BulkSmsGroup;
use Workdo\BulkSMS\Models\BulkSmsContact;
use Illuminate\Database\Seeder;

class DemoBulksmsSendSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BulksmsSend::where('created_by', $userId)->exists() && BulksmsSendMessage::where('created_by', $userId)->exists()) {
            return; // Skip seeding if data already exists
        }
        $messages = [
            'Welcome to our premium service! Your group membership is now active.',
            'Reminder: Team meeting scheduled for tomorrow at 10 AM.',
            'Special discount: 25% off for all group members this week only.',
            'System update: New features have been added to your account.',
            'Event notification: Annual conference registration is now open.',
            'Payment reminder: Your subscription renewal is due in 5 days.',
            'Security alert: Please update your password for better security.',
            'Holiday greetings: Wishing you and your family a wonderful season.',
            'Training session: Join us for the upcoming workshop next Friday.',
            'Survey request: Help us improve by sharing your feedback.',
            'Product launch: Introducing our latest innovation for your business.',
            'Maintenance notice: System will be down for 2 hours tonight.',
            'Birthday offer: Celebrate with 30% off your next purchase.',
            'Webinar invitation: Join our expert panel discussion next week.',
            'Flash sale: Limited time offer - 50% off selected items today.'
        ];

        $groups = BulkSmsGroup::where('created_by', $userId)->get();
        
        for ($i = 0; $i < 15; $i++) {
            $group = $groups->get($i % $groups->count());
            $contacts = is_string($group->contacts) ? json_decode($group->contacts, true) : $group->contacts;
            $contacts = $contacts ?? [];
            
            $mobileNumbers = [];
            foreach ($contacts as $contactId) {
                $contact = BulkSmsContact::where('id', $contactId)->where('created_by', $userId)->first();
                if ($contact) {
                    $mobileNumbers[] = $contact->mobile_no;
                }
            }

            $bulksmsSend = BulksmsSend::create([
                'group_id' => $group->id,
                'mobile_no' => implode(',', $mobileNumbers),
                'sms' => $messages[$i],
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);

            foreach ($contacts as $contactId) {
                $contact = BulkSmsContact::where('id', $contactId)->where('created_by', $userId)->first();
                if ($contact) {
                    BulksmsSendMessage::create([
                        'name' => $contact->name,
                        'group_id' => $group->id,
                        'mobile_no' => $contact->mobile_no,
                        'sms' => $messages[$i],
                        'status' => $i % 2 == 0 ? 'delivered' : 'failed',
                        'creator_id' => $userId,
                        'created_by' => $userId,
                    ]);
                }
            }
        }
    }
}