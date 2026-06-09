<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\Bookings\Models\BookingSocialLink;

class BookingSocialLinkSeeder extends Seeder
{
    public function run($userId)
    {
        if (!empty($userId)) {
            $this->createSocialLinksForUser($userId);
        }
    }
    
    private function createSocialLinksForUser($userId)
    {
        // Check if social links already exist for this user
        if (BookingSocialLink::where('created_by', $userId)->exists()) {
            return;
        }
        
        $socialLinks = [
            [
                'name' => 'Facebook',
                'link' => 'https://facebook.com/beautysalon',
                'icon' => 'Facebook',
                'created_by' => $userId,
                'creator_id' => $userId,
            ],
            [
                'name' => 'Instagram',
                'link' => 'https://instagram.com/beautysalon',
                'icon' => 'Instagram',
                'created_by' => $userId,
                'creator_id' => $userId,
            ],
            [
                'name' => 'Twitter',
                'link' => 'https://twitter.com/beautysalon',
                'icon' => 'Twitter',
                'created_by' => $userId,
                'creator_id' => $userId,
            ],
            [
                'name' => 'LinkedIn',
                'link' => 'https://linkedin.com/company/beautysalon',
                'icon' => 'Linkedin',
                'created_by' => $userId,
                'creator_id' => $userId,
            ]
        ];

        foreach ($socialLinks as $link) {
            BookingSocialLink::create($link);
        }
    }
}