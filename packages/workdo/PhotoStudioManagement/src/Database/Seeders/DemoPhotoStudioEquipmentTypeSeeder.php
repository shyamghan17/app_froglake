<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentType;

class DemoPhotoStudioEquipmentTypeSeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioEquipmentType::where('created_by', $userId)->exists()) {
            return;
        }

        $types = [
            ['name' => 'Cameras',    'description' => 'Camera bodies for capturing professional photographs.'],
            ['name' => 'Lenses',     'description' => 'Interchangeable lenses for various focal lengths and styles.'],
            ['name' => 'Lighting',   'description' => 'Studio and portable lighting equipment for perfect exposure.'],
            ['name' => 'Accessories','description' => 'Supporting accessories to enhance your photography setup.'],
        ];

        foreach ($types as $type) {
            PhotoStudioEquipmentType::create([
                'name'        => $type['name'],
                'description' => $type['description'],
                'status'      => true,
                'creator_id'  => $userId,
                'created_by'  => $userId,
            ]);
        }
    }
}
