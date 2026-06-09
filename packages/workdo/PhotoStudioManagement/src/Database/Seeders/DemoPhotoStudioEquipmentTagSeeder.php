<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentTag;

class DemoPhotoStudioEquipmentTagSeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioEquipmentTag::where('created_by', $userId)->exists()) {
            return;
        }

        $tags = [
            ['name' => 'DSLR',         'description' => 'Digital single-lens reflex camera body'],
            ['name' => 'Mirrorless',   'description' => 'Compact mirrorless camera system'],
            ['name' => 'Wide Angle',   'description' => 'Wide angle lens for expansive shots'],
            ['name' => 'Telephoto',    'description' => 'Telephoto lens for distant subjects'],
            ['name' => 'Prime Lens',   'description' => 'Fixed focal length prime lens'],
            ['name' => 'Zoom Lens',    'description' => 'Variable focal length zoom lens'],
            ['name' => 'Tripod',       'description' => 'Three-legged camera stabilizer'],
            ['name' => 'Monopod',      'description' => 'Single-legged camera support'],
            ['name' => 'Softbox',      'description' => 'Soft diffused studio lighting'],
            ['name' => 'Ring Light',   'description' => 'Circular LED ring light for portraits'],
            ['name' => 'Reflector',    'description' => 'Light reflector panel for fill light'],
            ['name' => 'Flash',        'description' => 'External flash unit for extra light'],
            ['name' => 'Drone',        'description' => 'Aerial drone for overhead photography'],
            ['name' => 'Action Camera','description' => 'Compact rugged action camera'],
            ['name' => 'Film Camera',  'description' => 'Analog film camera for classic photography'],
        ];

        foreach ($tags as $tag) {
            PhotoStudioEquipmentTag::create([
                'name'        => $tag['name'],
                'description' => $tag['description'],
                'status'      => true,
                'creator_id'  => $userId,
                'created_by'  => $userId,
            ]);
        }
    }
}
