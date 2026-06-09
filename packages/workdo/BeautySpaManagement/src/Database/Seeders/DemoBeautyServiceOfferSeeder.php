<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Workdo\BeautySpaManagement\Models\BeautyServiceOffer;
use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyService;
use Carbon\Carbon;

class DemoBeautyServiceOfferSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyServiceOffer::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            // Get existing beauty services to link offers
            $services = BeautyService::where('created_by', $userId)->get();
            
            if ($services->isEmpty()) {
                return; // No services available to link offers
            }

            // 15 realistic service offer records ordered oldest to newest (6 months)
            $offerRecords = [
                ['title' => 'New Year Glow Package', 'name' => 'January Facial Special', 'discount' => 25.00, 'start_date' => Carbon::now()->subDays(180), 'end_date' => Carbon::now()->subDays(150), 'description' => 'Start the new year with radiant skin featuring our signature facial treatment with complimentary skincare consultation and product samples for glowing results.'],
                ['title' => 'Valentine Romance Spa', 'name' => 'Couples Massage Deal', 'discount' => 30.00, 'start_date' => Carbon::now()->subDays(168), 'end_date' => Carbon::now()->subDays(138), 'description' => 'Romantic couples spa experience perfect for Valentine\'s Day featuring side-by-side massage therapy with champagne service and chocolate treats included.'],
                ['title' => 'Spring Renewal Offer', 'name' => 'Body Detox Package', 'discount' => 20.00, 'start_date' => Carbon::now()->subDays(156), 'end_date' => Carbon::now()->subDays(126), 'description' => 'Refresh your body for spring season with our comprehensive detox package including body wrap, exfoliation treatment, and lymphatic drainage massage therapy.'],
                ['title' => 'Bridal Beauty Bliss', 'name' => 'Wedding Preparation Special', 'discount' => 35.00, 'start_date' => Carbon::now()->subDays(144), 'end_date' => Carbon::now()->subDays(114), 'description' => 'Complete bridal beauty preparation package including trial makeup session, hair styling consultation, and skincare regimen for your perfect wedding day look.'],
                ['title' => 'Mother\'s Day Pamper', 'name' => 'Mom Appreciation Package', 'discount' => 40.00, 'start_date' => Carbon::now()->subDays(132), 'end_date' => Carbon::now()->subDays(102), 'description' => 'Special Mother\'s Day celebration package featuring relaxing massage, rejuvenating facial, and manicure service with complimentary gift wrapping and flowers.'],
                ['title' => 'Summer Glow Special', 'name' => 'Sun-Ready Skin Prep', 'discount' => 15.00, 'start_date' => Carbon::now()->subDays(120), 'end_date' => Carbon::now()->subDays(90), 'description' => 'Prepare your skin for summer with our brightening facial treatment, body exfoliation, and professional sunscreen consultation for healthy glowing skin.'],
                ['title' => 'Anti-Aging Intensive', 'name' => 'Youth Restoration Package', 'discount' => 45.00, 'start_date' => Carbon::now()->subDays(108), 'end_date' => Carbon::now()->subDays(78), 'description' => 'Comprehensive anti-aging treatment program featuring microdermabrasion, collagen therapy, and advanced skincare products for visible age-defying results and rejuvenation.'],
                ['title' => 'Back-to-School Refresh', 'name' => 'Student Beauty Package', 'discount' => 50.00, 'start_date' => Carbon::now()->subDays(96), 'end_date' => Carbon::now()->subDays(66), 'description' => 'Special student discount package featuring express facial, eyebrow shaping, and skincare consultation perfect for back-to-school confidence and fresh appearance.'],
                ['title' => 'Autumn Wellness Retreat', 'name' => 'Fall Relaxation Special', 'discount' => 25.00, 'start_date' => Carbon::now()->subDays(84), 'end_date' => Carbon::now()->subDays(54), 'description' => 'Seasonal wellness package featuring aromatherapy massage, pumpkin spice body wrap, and stress relief treatments for autumn relaxation and mental rejuvenation.'],
                ['title' => 'Halloween Glam Night', 'name' => 'Special Event Makeup', 'discount' => 20.00, 'start_date' => Carbon::now()->subDays(72), 'end_date' => Carbon::now()->subDays(42), 'description' => 'Creative makeup artistry for Halloween events featuring special effects makeup, dramatic styling, and professional photography session for memorable transformation looks.'],
                ['title' => 'Thanksgiving Gratitude', 'name' => 'Family Spa Package', 'discount' => 30.00, 'start_date' => Carbon::now()->subDays(60), 'end_date' => Carbon::now()->subDays(30), 'description' => 'Family appreciation package offering group discounts for multiple services, perfect for Thanksgiving celebration with loved ones and creating shared wellness memories.'],
                ['title' => 'Holiday Sparkle Special', 'name' => 'Festive Beauty Package', 'discount' => 35.00, 'start_date' => Carbon::now()->subDays(48), 'end_date' => Carbon::now()->subDays(18), 'description' => 'Holiday season beauty package featuring glamorous makeup application, festive nail art, and hair styling perfect for holiday parties and celebrations.'],
                ['title' => 'New Year Resolution', 'name' => 'Wellness Commitment Package', 'discount' => 40.00, 'start_date' => Carbon::now()->subDays(36), 'end_date' => Carbon::now()->addDays(24), 'description' => 'Support your wellness resolutions with our comprehensive package including monthly treatments, skincare routine consultation, and lifestyle wellness coaching sessions.'],
                ['title' => 'Winter Skin Rescue', 'name' => 'Hydration Intensive Treatment', 'discount' => 25.00, 'start_date' => Carbon::now()->subDays(24), 'end_date' => Carbon::now()->addDays(36), 'description' => 'Combat winter dryness with intensive hydration treatments featuring deep moisturizing facials, body butter applications, and professional skincare product recommendations.'],
                ['title' => 'Early Bird Spring Special', 'name' => 'Pre-Season Beauty Prep', 'discount' => 30.00, 'start_date' => Carbon::now()->subDays(12), 'end_date' => Carbon::now()->addDays(48), 'description' => 'Get ready for spring season with our early bird special featuring skin renewal treatments, body contouring sessions, and seasonal beauty consultation services.']
            ];

            foreach ($offerRecords as $record) {
                // Select random service for offer
                $selectedService = $services->random();
                
                // Calculate offer price based on service price and discount
                $originalPrice = $selectedService->price;
                $discountAmount = ($originalPrice * $record['discount']) / 100;
                $offerPrice = $originalPrice - $discountAmount;

                BeautyServiceOffer::create([
                    'title' => $record['title'],
                    'name' => $record['name'],
                    'price' => $originalPrice,
                    'start_date' => $record['start_date']->toDateString(),
                    'end_date' => $record['end_date']->toDateString(),
                    'discount' => $record['discount'],
                    'offer_price' => $offerPrice,
                    'description' => $record['description'],
                    'beauty_service_id' => $selectedService->id,
                    'creator_id' => $userId,
                    'created_by' => $userId,
                    'created_at' => $record['start_date'],
                    'updated_at' => $record['start_date'],
                ]);
            }
        }
    }
}