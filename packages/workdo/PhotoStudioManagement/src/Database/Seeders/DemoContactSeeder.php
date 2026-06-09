<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Workdo\PhotoStudioManagement\Models\PhotoStudioContact;
use Illuminate\Database\Seeder;

class DemoContactSeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioContact::where('created_by', $userId)->exists()) {
            return; // Skip seeding if data already exists
        }

        $contacts = [
            ['first_name' => 'Emma', 'last_name' => 'Johnson', 'email' => 'emma.johnson@example.com', 'phone_number' => '+1234567890', 'message' => 'I would like to book a wedding photography session for next month. Can you provide pricing details?', 'received_date' => '2026-01-03'],
            ['first_name' => 'Michael', 'last_name' => 'Smith', 'email' => 'michael.smith@example.com', 'phone_number' => '+1987654321', 'message' => 'Do you offer family portrait sessions? We have a family of 6 including 2 young children.', 'received_date' => '2026-01-07'],
            ['first_name' => 'Sarah', 'last_name' => 'Davis', 'email' => 'sarah.davis@example.com', 'phone_number' => '+1122334455', 'message' => 'I need professional headshots for my LinkedIn profile. What packages do you offer?', 'received_date' => '2026-01-12'],
            ['first_name' => 'David', 'last_name' => 'Wilson', 'email' => 'david.wilson@example.com', 'phone_number' => '+1555666777', 'message' => 'Can I rent your studio space for a product photography shoot? I need it for 4 hours.', 'received_date' => '2026-01-16'],
            ['first_name' => 'Jessica', 'last_name' => 'Brown', 'email' => 'jessica.brown@example.com', 'phone_number' => '+1999888777', 'message' => 'I am interested in maternity photography. Do you have outdoor location options?', 'received_date' => '2026-01-21'],
            ['first_name' => 'Robert', 'last_name' => 'Miller', 'email' => 'robert.miller@example.com', 'phone_number' => '+1444555666', 'message' => 'What is your policy for rescheduling photo sessions due to weather conditions?', 'received_date' => '2026-01-26'],
            ['first_name' => 'Amanda', 'last_name' => 'Garcia', 'email' => 'amanda.garcia@example.com', 'phone_number' => '+1777888999', 'message' => 'I need graduation photos. Do you offer same-day editing and delivery?', 'received_date' => '2026-02-02'],
            ['first_name' => 'Christopher', 'last_name' => 'Martinez', 'email' => 'christopher.martinez@example.com', 'phone_number' => '+1333444555', 'message' => 'Can you photograph our corporate event? We expect about 100 attendees.', 'received_date' => '2026-02-07'],
            ['first_name' => 'Lisa', 'last_name' => 'Anderson', 'email' => 'lisa.anderson@example.com', 'phone_number' => '+1666777888', 'message' => 'I love your portfolio! Do you travel for destination wedding photography?', 'received_date' => '2026-02-11'],
            ['first_name' => 'James', 'last_name' => 'Taylor', 'email' => 'james.taylor@example.com', 'phone_number' => '+1222333444', 'message' => 'What camera equipment do you have available for rent? I need a full-frame DSLR.', 'received_date' => '2026-02-15'],
            ['first_name' => 'Maria', 'last_name' => 'Rodriguez', 'email' => 'maria.rodriguez@example.com', 'phone_number' => '+1111222333', 'message' => 'Do you offer photography workshops or classes for beginners?', 'received_date' => '2026-02-19'],
            ['first_name' => 'William', 'last_name' => 'Lee', 'email' => 'william.lee@example.com', 'phone_number' => '+1444333222', 'message' => 'I need photos for my modeling portfolio. What styling and makeup services do you provide?', 'received_date' => '2026-02-24'],
            ['first_name' => 'Jennifer', 'last_name' => 'White', 'email' => 'jennifer.white@example.com', 'phone_number' => '+1777666555', 'message' => 'Can you help with real estate photography? I have 5 properties that need professional photos.', 'received_date' => '2026-03-02'],
            ['first_name' => 'Daniel', 'last_name' => 'Harris', 'email' => 'daniel.harris@example.com', 'phone_number' => '+1888999000', 'message' => 'What is included in your newborn photography package? Do you provide props?', 'received_date' => '2026-03-06'],
            ['first_name' => 'Ashley', 'last_name' => 'Clark', 'email' => 'ashley.clark@example.com', 'phone_number' => '+1555444333', 'message' => 'I need engagement photos. Do you scout locations or do we choose our own?', 'received_date' => '2026-03-10'],
            ['first_name' => 'Matthew', 'last_name' => 'Lewis', 'email' => 'matthew.lewis@example.com', 'phone_number' => '+1666555444', 'message' => 'Can you photograph our anniversary party? It will be an evening outdoor event.', 'received_date' => '2026-03-14'],
            ['first_name' => 'Nicole', 'last_name' => 'Walker', 'email' => 'nicole.walker@example.com', 'phone_number' => '+1333222111', 'message' => 'Do you offer photo restoration services for old family photographs?', 'received_date' => '2026-03-18'],
            ['first_name' => 'Andrew', 'last_name' => 'Hall', 'email' => 'andrew.hall@example.com', 'phone_number' => '+1999000111', 'message' => 'I am a small business owner. Can you help with product photography for my online store?', 'received_date' => '2026-03-22'],
            ['first_name' => 'Stephanie', 'last_name' => 'Young', 'email' => 'stephanie.young@example.com', 'phone_number' => '+1222111000', 'message' => 'What are your rates for pet photography? I have two dogs and a cat.', 'received_date' => '2026-03-26'],
            ['first_name' => 'Ryan', 'last_name' => 'King', 'email' => 'ryan.king@example.com', 'phone_number' => '+1777888111', 'message' => 'Thank you for the amazing wedding photos! Our families loved them. Highly recommend your services!', 'received_date' => '2026-03-30']
        ];

        foreach ($contacts as $contactData) {
            PhotoStudioContact::create([
                'first_name' => $contactData['first_name'],
                'last_name' => $contactData['last_name'],
                'email' => $contactData['email'],
                'phone_number' => $contactData['phone_number'],
                'message' => $contactData['message'],
                'received_date' => $contactData['received_date'],
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }
}