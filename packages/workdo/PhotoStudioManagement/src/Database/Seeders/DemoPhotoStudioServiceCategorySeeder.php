<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\PhotoStudioManagement\Models\PhotoStudioServiceCategory;

class DemoPhotoStudioServiceCategorySeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioServiceCategory::where('created_by', $userId)->exists()) {
            return;
        }

        $categories = [
            ['name' => 'Wedding Photography',    'description' => 'Complete wedding day coverage including ceremony, reception, and portraits.'],
            ['name' => 'Portrait Sessions',      'description' => 'Professional portrait photography for individuals, couples, and families.'],
            ['name' => 'Commercial Photography', 'description' => 'High-end commercial photography for advertising, branding, and marketing campaigns.'],
            ['name' => 'Event Photography',      'description' => 'Full event coverage for corporate events, parties, and social gatherings.'],
            ['name' => 'Product Photography',    'description' => 'Detailed and creative product photography for e-commerce and catalogs.'],
            ['name' => 'Fashion Photography',    'description' => 'Editorial and lookbook photography for fashion brands and models.'],
            ['name' => 'Real Estate Photography','description' => 'Professional interior and exterior photography for property listings.'],
            ['name' => 'Newborn & Maternity',    'description' => 'Gentle and artistic newborn and maternity photography sessions.'],
            ['name' => 'Sports Photography',     'description' => 'High-speed action photography for sports events and athletes.'],
            ['name' => 'Food Photography',       'description' => 'Appetizing food and beverage photography for restaurants and brands.'],
        ];

        foreach ($categories as $category) {
            PhotoStudioServiceCategory::create([
                'name'        => $category['name'],
                'description' => $category['description'],
                'status'      => true,
                'creator_id'  => $userId,
                'created_by'  => $userId,
            ]);
        }
    }
}
