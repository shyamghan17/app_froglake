<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Workdo\BeautySpaManagement\Models\BeautyService;
use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyServiceType;
use App\Models\User;
use Carbon\Carbon;

class DemoBeautyServiceSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyService::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            // Get existing service types and staff to link services
            $serviceTypes = BeautyServiceType::where('created_by', $userId)->get();
            $staff = User::where('created_by', $userId)->where('type', 'staff')->get();
            
            if ($serviceTypes->isEmpty()) {
                return; // No service types available to link services
            }

            // 15 realistic beauty service records ordered oldest to newest (6 months)
            $serviceRecords = [
                ['name' => 'Facial Treatment', 'max_persons' => 1, 'price' => 89.99, 'time' => '1.00', 'description' => 'Deep cleansing European facial treatment featuring steam, extractions, and customized mask application for radiant, healthy-looking skin with professional skincare consultation.', 'image' => 'beauty-service-1.png', 'type_match' => 'Facial Treatments', 'date' => Carbon::now()->subDays(180)],
                ['name' => 'Massage Therapy', 'max_persons' => 1, 'price' => 119.99, 'time' => '1.30', 'description' => 'Traditional Swedish massage therapy using long flowing strokes and gentle pressure to promote relaxation, improve circulation, and relieve muscle tension throughout the body.', 'image' => 'beauty-service-2.png', 'type_match' => 'Massage Therapy', 'date' => Carbon::now()->subDays(168)],
                ['name' => 'Rich Chocolate Hair Color', 'max_persons' => 1, 'price' => 149.99, 'time' => '1.15', 'description' => 'Enhance your look with professional hair coloring and highlights tailored to your style. Rich and vibrant chocolate tones for a complete hair transformation.', 'image' => 'beauty-service-3.png', 'type_match' => 'Hair Services', 'date' => Carbon::now()->subDays(156)],
                ['name' => 'Hair Styling', 'max_persons' => 1, 'price' => 79.99, 'time' => '0.45', 'description' => 'Expert hair styling service including wash, cut, and professional styling using premium products and techniques for special occasions or everyday elegance.', 'image' => 'beauty-service-4.png', 'type_match' => 'Hair Services', 'date' => Carbon::now()->subDays(144)],
                ['name' => 'Manicure Pedicure', 'max_persons' => 1, 'price' => 69.99, 'time' => '1.30', 'description' => 'Complete nail care service featuring gel manicure and pedicure with cuticle care, nail shaping, and long-lasting gel polish application for beautiful hands and feet.', 'image' => 'beauty-service-5.png', 'type_match' => 'Nail Care', 'date' => Carbon::now()->subDays(132)],
                ['name' => 'Bridal Makeup', 'max_persons' => 1, 'price' => 199.99, 'time' => '2.00', 'description' => 'Professional bridal makeup service including consultation, trial application, and wedding day makeup using high-quality products for long-lasting, photogenic results.', 'image' => 'beauty-service-6.png', 'type_match' => 'Makeup Services', 'date' => Carbon::now()->subDays(120)],
                ['name' => 'Microdermabrasion', 'max_persons' => 1, 'price' => 129.99, 'time' => '1.00', 'description' => 'Advanced microdermabrasion treatment to reduce fine lines, improve skin texture, and promote cellular renewal for younger-looking, smoother skin with immediate results.', 'image' => 'beauty-service-7.png', 'type_match' => 'Skincare Treatments', 'date' => Carbon::now()->subDays(108)],
                ['name' => 'Chemical Peel', 'max_persons' => 1, 'price' => 179.99, 'time' => '0.45', 'description' => 'Professional chemical peel treatment targeting sun damage, acne scars, and aging signs to reveal fresh, renewed skin with improved tone and texture.', 'image' => 'beauty-service-8.png', 'type_match' => 'Anti-Aging Treatments', 'date' => Carbon::now()->subDays(96)],
                ['name' => 'Rejuvenating Hair Spa', 'max_persons' => 1, 'price' => 159.99, 'time' => '1.30', 'description' => 'Nourish and revitalize your scalp with this deep-conditioning hair spa treatment for healthy, lustrous hair.', 'image' => 'beauty-service-9.png', 'type_match' => 'Hair Services', 'date' => Carbon::now()->subDays(84)],
                ['name' => 'Hair Wash', 'max_persons' => 1, 'price' => 29.99, 'time' => '0.30', 'description' => 'Professional hair washing service with premium shampoo and conditioning treatment, including scalp massage for clean, healthy hair.', 'image' => 'beauty-service-10.png', 'type_match' => 'Hair Services', 'date' => Carbon::now()->subDays(72)],
                ['name' => 'Hair Cut', 'max_persons' => 1, 'price' => 59.99, 'time' => '1.00', 'description' => 'Professional hair cutting service with consultation, wash, precision cutting, and styling for a fresh, modern look tailored to face shape.', 'image' => 'beauty-service-11.png', 'type_match' => 'Hair Services', 'date' => Carbon::now()->subDays(60)],
                ['name' => 'Nail Art', 'max_persons' => 1, 'price' => 49.99, 'time' => '1.00', 'description' => 'Creative nail art designs with professional application, featuring intricate patterns, colors, and decorative elements for stunning nail aesthetics.', 'image' => 'beauty-service-12.png', 'type_match' => 'Nail Care', 'date' => Carbon::now()->subDays(48)],
                ['name' => 'Anti Aging Treatment', 'max_persons' => 1, 'price' => 199.99, 'time' => '1.30', 'description' => 'Advanced anti-aging treatment using professional techniques and products to reduce fine lines, wrinkles, and signs of aging for youthful skin.', 'image' => 'beauty-service-13.png', 'type_match' => 'Anti-Aging Treatments', 'date' => Carbon::now()->subDays(36)],
                ['name' => 'Aromatherapy', 'max_persons' => 1, 'price' => 139.99, 'time' => '1.15', 'description' => 'Holistic aromatherapy treatment combining essential oils, gentle massage, and meditation techniques to promote mental clarity, emotional balance, and physical relaxation.', 'image' => 'beauty-service-14.png', 'type_match' => 'Holistic Treatments', 'date' => Carbon::now()->subDays(24)],
                ['name' => 'Face Mask', 'max_persons' => 1, 'price' => 59.99, 'time' => '0.45', 'description' => 'Rejuvenating face mask treatment using premium ingredients to hydrate, nourish, and revitalize your skin for a healthy, glowing complexion.', 'image' => 'beauty-service-15.png', 'type_match' => 'Facial Treatments', 'date' => Carbon::now()->subDays(12)]
            ];

            foreach ($serviceRecords as $record) {
                // Find matching service type by name similarity
                $matchingType = $serviceTypes->first(function($type) use ($record) {
                    return stripos($type->name, explode(' ', $record['type_match'])[0]) !== false;
                });
                
                // If no specific match found, use random service type
                if (!$matchingType) {
                    $matchingType = $serviceTypes->random();
                }

                // Select random staff member if available
                $selectedStaff = $staff->isNotEmpty() ? $staff->random() : null;

                BeautyService::create([
                    'name' => $record['name'],
                    'max_bookable_persons' => $record['max_persons'],
                    'price' => $record['price'],
                    'time' => $record['time'],
                    'description' => $record['description'],
                    'service_image' => $record['image'],
                    'service_type_id' => $matchingType->id,
                    'staff_id' => $selectedStaff ? $selectedStaff->id : null,
                    'included_services' => null, // Can be populated later if needed
                    'creator_id' => $userId,
                    'created_by' => $userId,
                    'created_at' => $record['date'],
                    'updated_at' => $record['date'],
                ]);
            }
        }
    }
}