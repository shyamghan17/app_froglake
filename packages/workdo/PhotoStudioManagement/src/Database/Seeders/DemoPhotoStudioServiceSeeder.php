<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\PhotoStudioManagement\Models\PhotoStudioService;
use Workdo\PhotoStudioManagement\Models\PhotoStudioServiceCategory;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCameraKit;

class DemoPhotoStudioServiceSeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioService::where('created_by', $userId)->exists()) {
            return;
        }

        $categoryIds = PhotoStudioServiceCategory::where('created_by', $userId)
            ->where('status', true)
            ->pluck('id')
            ->toArray();

        $cameraKitIds = PhotoStudioCameraKit::where('created_by', $userId)
            ->where('status', 'available')
            ->pluck('id')
            ->toArray();

        if (empty($categoryIds)) {
            return;
        }

        $services = [
            [
                'name'        => 'Wedding Photography Package',
                'image'       => 'photostudio-service-1.png',
                'description' => 'Complete wedding day coverage including ceremony, reception, portraits, and candid moments.',
                'price'       => 1500.00,
                'status'      => true,
            ],
            [
                'name'        => 'Portrait Session',
                'image'       => 'photostudio-service-2.png',
                'description' => 'Professional portrait photography for individuals, couples, and families in studio or on location.',
                'price'       => 300.00,
                'status'      => true,
            ],
            [
                'name'        => 'Commercial Photography',
                'image'       => 'photostudio-service-3.png',
                'description' => 'High-end commercial photography for advertising, branding, and marketing campaigns.',
                'price'       => 2000.00,
                'status'      => true,
            ],
            [
                'name'        => 'Event Photography',
                'image'       => 'photostudio-gallery-1.png',
                'description' => 'Full event coverage for corporate events, parties, and social gatherings.',
                'price'       => 800.00,
                'status'      => true,
            ],
            [
                'name'        => 'Product Photography',
                'image'       => 'photostudio-gallery-2.png',
                'description' => 'Detailed and creative product photography for e-commerce and catalogs.',
                'price'       => 500.00,
                'status'      => false,
            ],
            [
                'name'        => 'Fashion Photography',
                'image'       => 'photostudio-gallery-3.png',
                'description' => 'Editorial and lookbook photography for fashion brands, designers, and models.',
                'price'       => 1200.00,
                'status'      => true,
            ],
            [
                'name'        => 'Real Estate Photography',
                'image'       => 'photostudio-gallery-1.png',
                'description' => 'Professional interior and exterior photography for property listings and real estate marketing.',
                'price'       => 450.00,
                'status'      => true,
            ],
            [
                'name'        => 'Newborn & Maternity Session',
                'image'       => 'photostudio-gallery-2.png',
                'description' => 'Gentle and artistic newborn and maternity photography sessions in a safe, comfortable environment.',
                'price'       => 350.00,
                'status'      => true,
            ],
            [
                'name'        => 'Sports Photography',
                'image'       => 'photostudio-gallery-3.png',
                'description' => 'High-speed action photography for sports events, athletes, and team coverage.',
                'price'       => 700.00,
                'status'      => true,
            ],
            [
                'name'        => 'Food Photography',
                'image'       => 'photostudio-service-1.png',
                'description' => 'Appetizing food and beverage photography for restaurants, menus, and brand campaigns.',
                'price'       => 400.00,
                'status'      => false,
            ],
            [
                'name'        => 'Corporate Headshots',
                'image'       => 'photostudio-service-2.png',
                'description' => 'Professional headshot sessions for executives, teams, and LinkedIn profiles.',
                'price'       => 250.00,
                'status'      => true,
            ],
            [
                'name'        => 'Travel & Landscape Photography',
                'image'       => 'photostudio-service-3.png',
                'description' => 'Stunning travel and landscape photography capturing destinations, nature, and scenic environments.',
                'price'       => 600.00,
                'status'      => true,
            ],
        ];

        foreach ($services as $item) {
            PhotoStudioService::create([
                'name'                 => $item['name'],
                'image'                => $item['image'],
                'description'          => $item['description'],
                'price'                => $item['price'],
                'status'               => $item['status'],
                'service_category_ids' => $this->getRandomIds($categoryIds, rand(1, 2)),
                'camera_kit_ids'       => $this->getRandomIds($cameraKitIds, rand(1, 2)),
                'creator_id'           => $userId,
                'created_by'           => $userId,
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
