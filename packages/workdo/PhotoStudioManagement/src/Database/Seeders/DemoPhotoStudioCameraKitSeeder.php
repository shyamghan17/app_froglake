<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCameraKit;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentType;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentTag;

class DemoPhotoStudioCameraKitSeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioCameraKit::where('created_by', $userId)->exists()) {
            return;
        }

        $typeIds = PhotoStudioEquipmentType::where('created_by', $userId)
            ->where('status', true)
            ->pluck('id')
            ->toArray();

        $tagIds = PhotoStudioEquipmentTag::where('created_by', $userId)
            ->where('status', true)
            ->pluck('id')
            ->toArray();

        if (empty($typeIds) || empty($tagIds)) {
            return;
        }

        $kits = [
            [
                'name'           => 'Nikon Z9 Kit',
                'image'          => 'photostudio-kit-1.png',
                'description'    => 'Flagship full-frame mirrorless camera with 45.7MP stacked CMOS sensor and blackout-free shooting for sports and events.',
                'status'         => 'available',
                'specifications' => [
                    ['field_name' => 'Sensor',       'description' => '45.7MP Stacked CMOS'],
                    ['field_name' => 'ISO Range',    'description' => '64 - 25600 (expandable to 102400)'],
                    ['field_name' => 'Burst Speed',  'description' => '20fps RAW, 120fps JPEG'],
                    ['field_name' => 'Battery Life', 'description' => 'Approx. 740 shots per charge'],
                ],
            ],
            [
                'name'           => 'Canon EOS R5 Kit',
                'image'          => 'photostudio-camera-1.png',
                'description'    => 'Full-frame mirrorless camera body ideal for wedding and portrait photography with 45MP sensor.',
                'status'         => 'available',
                'specifications' => [
                    ['field_name' => 'Sensor',       'description' => '45MP Full-Frame CMOS'],
                    ['field_name' => 'ISO Range',    'description' => '100 - 51200 (expandable to 102400)'],
                    ['field_name' => 'Video',        'description' => '8K RAW, 4K 120fps'],
                    ['field_name' => 'Battery Life', 'description' => 'Approx. 320 shots per charge'],
                ],
            ],
            [
                'name'           => 'Sony A7 IV Kit',
                'image'          => 'photostudio-camera-2.png',
                'description'    => 'Versatile full-frame mirrorless camera with advanced autofocus and 33MP resolution for all-around photography.',
                'status'         => 'unavailable',
                'specifications' => [
                    ['field_name' => 'Sensor',       'description' => '33MP Full-Frame BSI CMOS'],
                    ['field_name' => 'ISO Range',    'description' => '100 - 51200 (expandable to 204800)'],
                    ['field_name' => 'Autofocus',    'description' => '759-point Phase Detection AF'],
                    ['field_name' => 'Battery Life', 'description' => 'Approx. 520 shots per charge'],
                ],
            ],
            [
                'name'           => '50mm f/1.8 Standard Prime Lens',
                'image'          => 'photostudio-kit-3.png',
                'description'    => 'Affordable and sharp standard prime lens delivering natural perspective and excellent low-light performance.',
                'status'         => 'available',
                'specifications' => [
                    ['field_name' => 'Focal Length', 'description' => '50mm'],
                    ['field_name' => 'Aperture',     'description' => 'f/1.8 maximum'],
                    ['field_name' => 'Filter Size',  'description' => '58mm'],
                    ['field_name' => 'Weight',       'description' => '160g'],
                ],
            ],
            [
                'name'           => '24-70mm f/2.8 Zoom Lens',
                'image'          => 'photostudio-lense-1.png',
                'description'    => 'Professional standard zoom lens covering wide to short telephoto range, perfect for events and portraits.',
                'status'         => 'available',
                'specifications' => [
                    ['field_name' => 'Focal Length',     'description' => '24-70mm'],
                    ['field_name' => 'Aperture',         'description' => 'f/2.8 constant'],
                    ['field_name' => 'Filter Size',      'description' => '82mm'],
                    ['field_name' => 'Image Stabilizer', 'description' => 'Yes, 5-stop'],
                ],
            ],
            [
                'name'           => '85mm f/1.4 Portrait Lens',
                'image'          => 'photostudio-lense-2.png',
                'description'    => 'Classic portrait prime lens delivering beautiful background blur and sharp subject detail.',
                'status'         => 'unavailable',
                'specifications' => [
                    ['field_name' => 'Focal Length', 'description' => '85mm'],
                    ['field_name' => 'Aperture',     'description' => 'f/1.4 maximum'],
                    ['field_name' => 'Filter Size',  'description' => '77mm'],
                    ['field_name' => 'Bokeh',        'description' => '11-blade circular aperture'],
                ],
            ],
            [
                'name'           => '16-35mm f/4 Wide Angle Lens',
                'image'          => 'photostudio-lense-3.png',
                'description'    => 'Ultra-wide zoom lens for landscapes, architecture, and environmental portraits.',
                'status'         => 'available',
                'specifications' => [
                    ['field_name' => 'Focal Length',     'description' => '16-35mm'],
                    ['field_name' => 'Aperture',         'description' => 'f/4 constant'],
                    ['field_name' => 'Filter Size',      'description' => '72mm'],
                    ['field_name' => 'Image Stabilizer', 'description' => 'Yes, 4-stop'],
                ],
            ],
            [
                'name'           => 'Strobe Flash Monolight Kit',
                'image'          => 'photostudio-kit-2.png',
                'description'    => 'Powerful monolight strobe kit for high-speed studio flash photography with precise color accuracy.',
                'status'         => 'available',
                'specifications' => [
                    ['field_name' => 'Power Output', 'description' => '400Ws'],
                    ['field_name' => 'Recycle Time', 'description' => '0.5 - 1.8 seconds'],
                    ['field_name' => 'Color Temp',   'description' => '5600K ±100K'],
                    ['field_name' => 'Sync Speed',   'description' => 'Up to 1/250s'],
                ],
            ],
            [
                'name'           => 'Studio Softbox Lighting Kit',
                'image'          => 'photostudio-light-1.png',
                'description'    => 'Professional two-point softbox lighting setup for even, diffused studio illumination.',
                'status'         => 'available',
                'specifications' => [
                    ['field_name' => 'Power Output', 'description' => '2x 85W LED Bulbs'],
                    ['field_name' => 'Softbox Size', 'description' => '90x90cm each'],
                    ['field_name' => 'Color Temp',   'description' => '5500K Daylight'],
                    ['field_name' => 'Stand Height', 'description' => 'Adjustable up to 200cm'],
                ],
            ],
            [
                'name'           => 'Portable LED Ring Light Kit',
                'image'          => 'photostudio-light-2.png',
                'description'    => 'Compact ring light kit for portrait, beauty, and video content with adjustable color temperature.',
                'status'         => 'unavailable',
                'specifications' => [
                    ['field_name' => 'Diameter',   'description' => '18 inch / 46cm'],
                    ['field_name' => 'Power',      'description' => '55W LED'],
                    ['field_name' => 'Color Temp', 'description' => '3200K - 5600K adjustable'],
                    ['field_name' => 'Brightness', 'description' => '10 levels dimmable'],
                ],
            ],
            [
                'name'           => 'Remote Shutter & Trigger Kit',
                'image'          => 'photostudio-kit-4.png',
                'description'    => 'Wireless remote shutter release and flash trigger set for hands-free and multi-light photography.',
                'status'         => 'available',
                'specifications' => [
                    ['field_name' => 'Range',         'description' => 'Up to 100m wireless'],
                    ['field_name' => 'Channels',      'description' => '16 selectable channels'],
                    ['field_name' => 'Compatibility', 'description' => 'Canon, Nikon, Sony, Fujifilm'],
                    ['field_name' => 'Battery',       'description' => '2x AA batteries'],
                ],
            ],
            [
                'name'           => 'Carbon Fiber Tripod Kit',
                'image'          => 'photostudio-accessories-1.png',
                'description'    => 'Lightweight carbon fiber tripod with ball head for stable shooting in studio and outdoor environments.',
                'status'         => 'available',
                'specifications' => [
                    ['field_name' => 'Material',      'description' => 'Carbon Fiber'],
                    ['field_name' => 'Max Height',    'description' => '165cm'],
                    ['field_name' => 'Load Capacity', 'description' => '10kg'],
                    ['field_name' => 'Weight',        'description' => '1.2kg'],
                ],
            ],
            [
                'name'           => 'Camera Bag Pro Kit',
                'image'          => 'photostudio-accessories-2.png',
                'description'    => 'Padded professional camera backpack with customizable dividers for safe transport of gear.',
                'status'         => 'available',
                'specifications' => [
                    ['field_name' => 'Capacity',   'description' => '25L'],
                    ['field_name' => 'Fits',       'description' => '1 body + 4 lenses + laptop 15"'],
                    ['field_name' => 'Material',   'description' => 'Water-resistant nylon'],
                    ['field_name' => 'Dimensions', 'description' => '30x20x45cm'],
                ],
            ],
            [
                'name'           => 'Memory & Filter Accessory Kit',
                'image'          => 'photostudio-accessories-3.png',
                'description'    => 'Essential accessory bundle including high-speed memory cards, ND filters, and cleaning kit.',
                'status'         => 'unavailable',
                'specifications' => [
                    ['field_name' => 'Memory Cards', 'description' => '2x 128GB CFexpress Type B'],
                    ['field_name' => 'ND Filters',   'description' => 'ND4, ND8, ND16, ND64 set'],
                    ['field_name' => 'CPL Filter',   'description' => '77mm Circular Polarizer'],
                    ['field_name' => 'Cleaning Kit', 'description' => 'Sensor swabs, blower, microfiber cloth'],
                ],
            ],
        ];

        foreach ($kits as $kit) {
            PhotoStudioCameraKit::create([
                'name'              => $kit['name'],
                'image'             => $kit['image'],
                'description'       => $kit['description'],
                'tags'              => $this->getRandomIds($tagIds, rand(2, 3)),
                'specifications'    => $kit['specifications'],
                'equipment_type_id' => $typeIds[array_rand($typeIds)],
                'status'            => $kit['status'],
                'creator_id'        => $userId,
                'created_by'        => $userId,
            ]);
        }
    }

    private function getRandomIds(array $ids, int $count): array
    {
        if (empty($ids)) {
            return [];
        }
        $count = min($count, count($ids));
        $selected = array_rand(array_flip($ids), $count);
        $selected = is_array($selected) ? $selected : [$selected];
        return array_map('strval', $selected);
    }
}
