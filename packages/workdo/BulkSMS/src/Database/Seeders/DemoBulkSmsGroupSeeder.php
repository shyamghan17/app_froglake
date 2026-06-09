<?php

namespace Workdo\BulkSMS\Database\Seeders;

use Workdo\BulkSMS\Models\BulkSmsGroup;
use Illuminate\Database\Seeder;
use Workdo\BulkSMS\Models\BulkSmsContact;


class DemoBulkSmsGroupSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BulkSmsGroup::where('created_by', $userId)->exists()) {
            return; // Skip seeding if data already exists
        }
        
        $groups = [
            ['name' => 'VIP Customers', 'contacts' => ['1', '2', '3', '4']],
            ['name' => 'Marketing Team', 'contacts' => ['5', '6', '7']],
            ['name' => 'Sales Prospects', 'contacts' => ['8', '9', '10', '11', '12']],
            ['name' => 'Support Staff', 'contacts' => ['13', '14', '15']],
            ['name' => 'Premium Members', 'contacts' => ['1', '5', '9', '13']],
            ['name' => 'Newsletter Subscribers', 'contacts' => ['2', '4', '6', '8', '10', '12', '14']],
            ['name' => 'Event Attendees', 'contacts' => ['3', '7', '11', '15']],
            ['name' => 'Product Updates', 'contacts' => ['1', '3', '5', '7', '9', '11']],
            ['name' => 'Regional Managers', 'contacts' => ['2', '6', '10']],
            ['name' => 'Beta Testers', 'contacts' => ['4', '8', '12', '15']],
            ['name' => 'Corporate Clients', 'contacts' => ['1', '2', '5', '6']],
            ['name' => 'Training Group', 'contacts' => ['7', '8', '9', '10']],
            ['name' => 'Emergency Contacts', 'contacts' => ['11', '12', '13', '14', '15']],
            ['name' => 'Feedback Panel', 'contacts' => ['3', '6', '9', '12']],
            ['name' => 'Special Offers', 'contacts' => ['1', '4', '7', '10', '13']]
        ];

        $availableContacts = BulkSmsContact::where('created_by', $userId)->pluck('id')->toArray();
        
        foreach ($groups as $group) {
            // Filter contacts to only include existing ones and convert to strings
            $validContacts = array_intersect($group['contacts'], array_map('strval', $availableContacts));
            
            BulkSmsGroup::create([
                'name' => $group['name'],
                'contacts' => array_values($validContacts),
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }
}