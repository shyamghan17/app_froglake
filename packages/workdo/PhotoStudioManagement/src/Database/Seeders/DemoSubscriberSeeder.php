<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Workdo\PhotoStudioManagement\Models\PhotoStudioSubscriber;
use Illuminate\Database\Seeder;

class DemoSubscriberSeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioSubscriber::where('created_by', $userId)->exists()) {
            return; // Skip seeding if data already exists
        }

        $subscribers = [
            ['email' => 'photographer.john@example.com', 'subscribed_date' => '2026-01-03'],
            ['email' => 'sarah.portraits@example.com', 'subscribed_date' => '2026-01-07'],
            ['email' => 'wedding.michael@example.com', 'subscribed_date' => '2026-01-12'],
            ['email' => 'emily.events@example.com', 'subscribed_date' => '2026-01-16'],
            ['email' => 'david.studio@example.com', 'subscribed_date' => '2026-01-21'],
            ['email' => 'lisa.photography@example.com', 'subscribed_date' => '2026-01-26'],
            ['email' => 'robert.shoots@example.com', 'subscribed_date' => '2026-02-02'],
            ['email' => 'jennifer.lens@example.com', 'subscribed_date' => '2026-02-07'],
            ['email' => 'chris.capture@example.com', 'subscribed_date' => '2026-02-11'],
            ['email' => 'amanda.frames@example.com', 'subscribed_date' => '2026-02-15'],
            ['email' => 'james.focus@example.com', 'subscribed_date' => '2026-02-19'],
            ['email' => 'maria.shutter@example.com', 'subscribed_date' => '2026-02-24'],
            ['email' => 'william.photo@example.com', 'subscribed_date' => '2026-03-02'],
            ['email' => 'jessica.camera@example.com', 'subscribed_date' => '2026-03-06'],
            ['email' => 'daniel.visual@example.com', 'subscribed_date' => '2026-03-10'],
            ['email' => 'ashley.creative@example.com', 'subscribed_date' => '2026-03-14'],
            ['email' => 'matthew.artistic@example.com', 'subscribed_date' => '2026-03-18'],
            ['email' => 'nicole.memories@example.com', 'subscribed_date' => '2026-03-22'],
            ['email' => 'andrew.moments@example.com', 'subscribed_date' => '2026-03-26'],
            ['email' => 'stephanie.clicks@example.com', 'subscribed_date' => '2026-03-30']
        ];

        foreach ($subscribers as $subscriberData) {
            PhotoStudioSubscriber::create([
                'email' => $subscriberData['email'],
                'subscribed_date' => $subscriberData['subscribed_date'],
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }
}