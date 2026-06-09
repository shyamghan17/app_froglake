<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\ProductService\Models\ProductServiceItem;
use App\Models\User;
use Workdo\Bookings\Models\BookingDuration;
use Workdo\ProductService\Models\ProductServiceCategory;
use Workdo\ProductService\Models\ProductServiceUnit;
use Workdo\ProductService\Models\ProductServiceTax;

class BookingItemSeeder extends Seeder
{
    public function run($userId)
    {
        if (!empty($userId)) {
            $this->createItemsForUser($userId);
        }
    }
    
    private function createItemsForUser($userId)
    {
        // Check if we need to create new items (only if none exist)
        if (ProductServiceItem::where('created_by', $userId)->where('type', 'bookings')->count() >= 15) {
            return;
        }
        
        // Create booking-specific categories
        $bookingCategories = [
            ['name' => 'Hair Services', 'color' => '#EC4899'],
            ['name' => 'Facial & Skin Care', 'color' => '#8B5CF6'],
            ['name' => 'Massage & Therapy', 'color' => '#10B981'],
            ['name' => 'Nail Care', 'color' => '#F59E0B'],
            ['name' => 'Makeup & Beauty', 'color' => '#3B82F6'],
        ];
        
        $categoryIds = [];
        foreach ($bookingCategories as $cat) {
            $category = ProductServiceCategory::firstOrCreate(
                ['name' => $cat['name'], 'created_by' => $userId],
                ['color' => $cat['color'], 'creator_id' => $userId]
            );
            $categoryIds[$cat['name']] = $category->id;
        }
        
        // Create booking-specific units
        $bookingUnits = ['Session', 'Hour'];
        $unitIds = [];
        foreach ($bookingUnits as $unitName) {
            $unit = ProductServiceUnit::firstOrCreate(
                ['unit_name' => $unitName, 'created_by' => $userId],
                ['creator_id' => $userId]
            );
            $unitIds[$unitName] = $unit->id;
        }
        
        $taxes = ProductServiceTax::where('created_by', $userId)->pluck('id')->toArray();
        
        $items = [
            ['name' => 'Hair Cut & Styling', 'category' => 'Hair Services', 'unit' => 'Session', 'description' => 'Professional hair cutting and styling service with expert stylists.', 'image' => 'booking_haircut_styling_main.png', 'images' => ["booking_haircut_gallery_1.png", "booking_haircut_gallery_2.jpg"]],
            ['name' => 'Facial Treatment', 'category' => 'Facial & Skin Care', 'unit' => 'Session', 'description' => 'Rejuvenating facial treatment for healthy and glowing skin.', 'image' => 'booking_facial_treatment_main.png', 'images' => ["booking_facial_gallery_1.png", "booking_facial_gallery_2.jpg"]],
            ['name' => 'Massage Therapy', 'category' => 'Massage & Therapy', 'unit' => 'Hour', 'description' => 'Relaxing full body massage therapy to relieve stress and tension.', 'image' => 'booking_massage_therapy_main.jpg', 'images' => ["booking_massage_gallery_1.png", "booking_massage_gallery_2.png"]],
            ['name' => 'Manicure & Pedicure', 'category' => 'Nail Care', 'unit' => 'Session', 'description' => 'Complete nail care service including manicure and pedicure.', 'image' => 'booking_manicure_pedicure_main.png', 'images' => ["booking_nail_care_gallery_1.png","booking_nail_care_gallery_2.png"]],
            ['name' => 'Eyebrow Threading', 'category' => 'Makeup & Beauty', 'unit' => 'Session', 'description' => 'Precise eyebrow shaping and threading service.', 'image' => 'booking_eyebrow_threading_main.png', 'images' => ["booking_eyebrow_gallery_1.jpg","booking_eyebrow_gallery_2.jpg"]],
            ['name' => 'Makeup Service', 'category' => 'Makeup & Beauty', 'unit' => 'Session', 'description' => 'Professional makeup application for special occasions.', 'image' => 'booking_makeup_service_main.jpg', 'images' => ["booking_makeup_gallery_1.png", "booking_makeup_gallery_2.png"]],
            ['name' => 'Deep Tissue Massage', 'category' => 'Massage & Therapy', 'unit' => 'Hour', 'description' => 'Therapeutic deep tissue massage for muscle tension relief.', 'image' => 'booking_deep_tissue_massage_main.png', 'images' => ["booking_deep_massage_gallery_1.png", "booking_deep_massage_gallery_2.jpg"]],
            ['name' => 'Bridal Makeup', 'category' => 'Makeup & Beauty', 'unit' => 'Session', 'description' => 'Complete bridal makeup package for your special day.', 'image' => 'booking_bridal_makeup_main.png', 'images' => ["booking_bridal_gallery_1.png","booking_bridal_gallery_2.png"]],
            ['name' => 'Hair Coloring', 'category' => 'Hair Services', 'unit' => 'Session', 'description' => 'Professional hair coloring and highlighting services.', 'image' => 'booking_hair_coloring_main.jpg', 'images' => ["booking_hair_color_gallery_1.png","booking_hair_color_gallery_2.png"]],
            ['name' => 'Acne Treatment', 'category' => 'Facial & Skin Care', 'unit' => 'Session', 'description' => 'Specialized facial treatment for acne-prone skin.', 'image' => 'booking_acne_treatment_main.png', 'images' => ["booking_acne_gallery_1.jpg", "booking_acne_gallery_2.png"]],
            ['name' => 'Gel Nail Extensions', 'category' => 'Nail Care', 'unit' => 'Session', 'description' => 'Long-lasting gel nail extensions with custom designs.', 'image' => 'booking_gel_nail_extensions_main.png', 'images' => ["booking_gel_nails_gallery_1.png", "booking_gel_nails_gallery_2.jpg"]],
            ['name' => 'Aromatherapy Massage', 'category' => 'Massage & Therapy', 'unit' => 'Hour', 'description' => 'Relaxing aromatherapy massage with essential oils.', 'image' => 'booking_aromatherapy_massage_main.jpg', 'images' => ["booking_aromatherapy_gallery_1.png","booking_aromatherapy_gallery_2.png"]],
            ['name' => 'Microblading', 'category' => 'Makeup & Beauty', 'unit' => 'Session', 'description' => 'Semi-permanent eyebrow enhancement technique.', 'image' => 'booking_microblading_main.png', 'images' => ["booking_microblading_gallery_1.png","booking_microblading_gallery_2.png"]],
            ['name' => 'Hair Keratin Treatment', 'category' => 'Hair Services', 'unit' => 'Session', 'description' => 'Smoothing keratin treatment for frizzy hair.', 'image' => 'booking_keratin_treatment_main.png', 'images' => ["booking_keratin_gallery_1.jpg", "booking_keratin_gallery_2.png"]],
            ['name' => 'Anti-Aging Facial', 'category' => 'Facial & Skin Care', 'unit' => 'Session', 'description' => 'Advanced anti-aging facial with premium skincare products.', 'image' => 'booking_anti_aging_facial_main.jpg', 'images' => ["booking_anti_aging_gallery_1.png", "booking_anti_aging_gallery_2.png"]]
        ];

        foreach ($items as $index => $item) {
            $randomTaxIds = null;

            if (!empty($taxes)) {
                $pickCount = rand(2, min(3, count($taxes)));

                $randomKeys = (array) array_rand($taxes, $pickCount);

                $randomTaxIds = array_values(
                    array_map(
                        'intval',
                        array_intersect_key($taxes, array_flip($randomKeys))
                    )
                );
            }

            $createdItem = ProductServiceItem::create([
                'name' => $item['name'],
                'sku' => 'BKG-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'description' => $item['description'],
                'sale_price' => rand(15, 150),
                'purchase_price' => rand( 25, 200),
                'category_id' => $categoryIds[$item['category']] ?? null,
                'unit' => $unitIds[$item['unit']] ?? null,
                'tax_ids' => $randomTaxIds,
                'image' => $item['image'],
                'images' => json_encode($item['images']),
                'type' => 'bookings',
                'created_by' => $userId,
                'creator_id' => $userId,
            ]);
            
            // Create booking duration for each item
            BookingDuration::create([
                'item_id' => $createdItem->id,
                'duration' => ['00:30', '01:00', '01:30', '02:00'][rand(0, 3)],
                'total_slots' => rand(1, 5),
                'created_by' => $userId,
                'creator_id' => $userId,
            ]);
        }
    }
}