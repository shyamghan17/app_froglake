<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\PhotoStudioManagement\Models\PhotoStudioGalleryType;

class DemoPhotoStudioGalleryTypeSeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioGalleryType::where('created_by', $userId)->exists()) {
            return;
        }

        $types = [
            ['name' => 'Wedding',      'description' => 'Beautiful wedding photography capturing every precious moment.'],
            ['name' => 'Portrait',     'description' => 'Professional portrait sessions for individuals and families.'],
            ['name' => 'Commercial',   'description' => 'High-quality commercial photography for brands and businesses.'],
            ['name' => 'Event',        'description' => 'Dynamic event photography for corporate and social gatherings.'],
            ['name' => 'Fashion',      'description' => 'Creative fashion photography for models and clothing brands.'],
            ['name' => 'Product',      'description' => 'Detailed product photography for e-commerce and marketing.'],
            ['name' => 'Architecture', 'description' => 'Stunning architectural and interior photography.'],
            ['name' => 'Nature',       'description' => 'Breathtaking nature and landscape photography.'],
            ['name' => 'Sports',       'description' => 'Action-packed sports and fitness photography.'],
            ['name' => 'Newborn',      'description' => 'Tender newborn and maternity photography sessions.'],
        ];

        foreach ($types as $type) {
            PhotoStudioGalleryType::create([
                'name'        => $type['name'],
                'description' => $type['description'],
                'status'      => true,
                'creator_id'  => $userId,
                'created_by'  => $userId,
            ]);
        }
    }
}
