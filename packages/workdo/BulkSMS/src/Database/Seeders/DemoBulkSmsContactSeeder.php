<?php

namespace Workdo\BulkSMS\Database\Seeders;

use Workdo\BulkSMS\Models\BulkSmsContact;
use Illuminate\Database\Seeder;



class DemoBulkSmsContactSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BulkSmsContact::where('created_by', $userId)->exists()) {
            return; // Skip seeding if data already exists
        }
        
        $contacts = [
            ['name' => 'Alexander Thompson', 'email' => 'alex.thompson@techcorp.com', 'mobile_no' => '+44-20-7946-0958', 'city' => 'London', 'state' => 'England', 'zip_code' => 'SW1A 1AA'],
            ['name' => 'Maria Rodriguez', 'email' => 'maria.rodriguez@healthcare.org', 'mobile_no' => '+34-91-123-4567', 'city' => 'Madrid', 'state' => 'Madrid', 'zip_code' => '28001'],
            ['name' => 'James Chen', 'email' => 'j.chen@university.edu', 'mobile_no' => '+86-138-0013-8000', 'city' => 'Beijing', 'state' => 'Beijing', 'zip_code' => '100000'],
            ['name' => 'Sarah Williams', 'email' => 'sarah.w@consulting.biz', 'mobile_no' => '+61-2-9374-4000', 'city' => 'Sydney', 'state' => 'NSW', 'zip_code' => '2000'],
            ['name' => 'Mohammed Al-Hassan', 'email' => 'mohammed.hassan@finance.com', 'mobile_no' => '+971-4-123-4567', 'city' => 'Dubai', 'state' => 'Dubai', 'zip_code' => '00000'],
            ['name' => 'Jennifer Park', 'email' => 'jennifer.park@startup.io', 'mobile_no' => '+82-2-123-4567', 'city' => 'Seoul', 'state' => 'Seoul', 'zip_code' => '04524'],
            ['name' => 'Robert O\'Connor', 'email' => 'rob.oconnor@manufacturing.net', 'mobile_no' => '+49-30-12345678', 'city' => 'Berlin', 'state' => 'Berlin', 'zip_code' => '10115'],
            ['name' => 'Lisa Zhang', 'email' => 'lisa.zhang@research.gov', 'mobile_no' => '+81-3-1234-5678', 'city' => 'Tokyo', 'state' => 'Tokyo', 'zip_code' => '100-0001'],
            ['name' => 'Carlos Mendoza', 'email' => 'carlos.mendoza@retail.store', 'mobile_no' => '+52-55-1234-5678', 'city' => 'Mexico City', 'state' => 'CDMX', 'zip_code' => '01000'],
            ['name' => 'Emma Johnson', 'email' => 'emma.johnson@nonprofit.org', 'mobile_no' => '+1-416-123-4567', 'city' => 'Toronto', 'state' => 'Ontario', 'zip_code' => 'M5H 2N2'],
            ['name' => 'David Kumar', 'email' => 'david.kumar@software.dev', 'mobile_no' => '+91-11-2345-6789', 'city' => 'New Delhi', 'state' => 'Delhi', 'zip_code' => '110001'],
            ['name' => 'Rachel Green', 'email' => 'rachel.green@marketing.agency', 'mobile_no' => '+33-1-42-34-56-78', 'city' => 'Paris', 'state' => 'Île-de-France', 'zip_code' => '75001'],
            ['name' => 'Antonio Silva', 'email' => 'antonio.silva@construction.co', 'mobile_no' => '+55-11-9876-5432', 'city' => 'São Paulo', 'state' => 'São Paulo', 'zip_code' => '01310-100'],
            ['name' => 'Michelle Lee', 'email' => 'michelle.lee@design.studio', 'mobile_no' => '+65-6123-4567', 'city' => 'Singapore', 'state' => 'Singapore', 'zip_code' => '018989'],
            ['name' => 'Thomas Anderson', 'email' => 'thomas.anderson@logistics.com', 'mobile_no' => '+31-20-123-4567', 'city' => 'Amsterdam', 'state' => 'North Holland', 'zip_code' => '1012 JS']
        ];

        foreach ($contacts as $contact) {
            BulkSmsContact::create([
                'name' => $contact['name'],
                'email' => $contact['email'],
                'mobile_no' => $contact['mobile_no'],
                'city' => $contact['city'],
                'state' => $contact['state'],
                'zip_code' => $contact['zip_code'],
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }
}