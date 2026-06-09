<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Workdo\BeautySpaManagement\Models\BeautyMembership;
use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyService;
use Carbon\Carbon;

class DemoBeautyMembershipSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyMembership::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            // Get existing beauty services to link memberships
            $services = BeautyService::where('created_by', $userId)->get();
            
            if ($services->isEmpty()) {
                return; // No services available to link memberships
            }

            // 30 realistic membership records ordered oldest to newest (6 months)
            $membershipRecords = [
                ['name' => 'Basic Beauty Package', 'duration' => 30, 'benefits' => 'Monthly facial, basic skincare consultation', 'price' => 89.99, 'description' => 'Perfect starter package for new clients featuring essential beauty treatments and professional skincare guidance for healthy glowing skin.', 'date' => Carbon::now()->subDays(180)],
                ['name' => 'Premium Facial Membership', 'duration' => 60, 'benefits' => 'Bi-weekly facials, product discounts', 'price' => 159.99, 'description' => 'Comprehensive facial care program including advanced treatments, premium products, and exclusive member discounts for optimal skin health.', 'date' => Carbon::now()->subDays(174)],
                ['name' => 'Wellness Spa Experience', 'duration' => 90, 'benefits' => 'Full spa access, massage therapy', 'price' => 249.99, 'description' => 'Complete wellness journey combining relaxation therapies, stress relief treatments, and holistic approaches to beauty and mental well-being.', 'date' => Carbon::now()->subDays(168)],
                ['name' => 'VIP Beauty Elite', 'duration' => 365, 'benefits' => 'Unlimited services, priority booking', 'price' => 1299.99, 'description' => 'Exclusive annual membership offering unlimited access to all premium services, priority scheduling, and personalized beauty consultations.', 'date' => Carbon::now()->subDays(162)],
                ['name' => 'Bridal Beauty Package', 'duration' => 180, 'benefits' => 'Wedding prep, makeup trials', 'price' => 599.99, 'description' => 'Specialized bridal preparation program including makeup trials, skincare regimen, hair styling sessions, and wedding day beauty coordination.', 'date' => Carbon::now()->subDays(156)],
                ['name' => 'Anti-Aging Renewal', 'duration' => 120, 'benefits' => 'Advanced treatments, collagen therapy', 'price' => 399.99, 'description' => 'Targeted anti-aging program featuring advanced skincare treatments, collagen therapy sessions, and age-defying beauty protocols for mature skin.', 'date' => Carbon::now()->subDays(150)],
                ['name' => 'Teen Beauty Basics', 'duration' => 45, 'benefits' => 'Acne treatment, skincare education', 'price' => 79.99, 'description' => 'Gentle introduction to skincare for teenagers including acne treatment, proper cleansing techniques, and age-appropriate beauty education.', 'date' => Carbon::now()->subDays(144)],
                ['name' => 'Executive Grooming', 'duration' => 90, 'benefits' => 'Professional styling, grooming services', 'price' => 199.99, 'description' => 'Professional grooming package designed for busy executives featuring efficient styling services, skincare maintenance, and polished appearance management.', 'date' => Carbon::now()->subDays(138)],
                ['name' => 'Maternity Glow Package', 'duration' => 270, 'benefits' => 'Prenatal safe treatments, relaxation', 'price' => 349.99, 'description' => 'Pregnancy-safe beauty treatments designed for expecting mothers including gentle facials, relaxation therapies, and safe skincare during pregnancy.', 'date' => Carbon::now()->subDays(132)],
                ['name' => 'Seasonal Refresh', 'duration' => 90, 'benefits' => 'Seasonal treatments, skin adaptation', 'price' => 179.99, 'description' => 'Quarterly beauty refresh program adapting treatments to seasonal changes, protecting skin from environmental factors, and maintaining year-round radiance.', 'date' => Carbon::now()->subDays(126)],
                ['name' => 'Couples Spa Retreat', 'duration' => 60, 'benefits' => 'Couples treatments, romantic ambiance', 'price' => 299.99, 'description' => 'Romantic spa experience for couples featuring synchronized treatments, private relaxation areas, and intimate wellness sessions for shared rejuvenation.', 'date' => Carbon::now()->subDays(120)],
                ['name' => 'Acne Solution Program', 'duration' => 120, 'benefits' => 'Specialized acne treatments, monitoring', 'price' => 229.99, 'description' => 'Comprehensive acne treatment program combining medical-grade skincare, professional extractions, and ongoing monitoring for clear healthy skin.', 'date' => Carbon::now()->subDays(114)],
                ['name' => 'Golden Years Beauty', 'duration' => 180, 'benefits' => 'Senior-friendly treatments, gentle care', 'price' => 189.99, 'description' => 'Specially designed beauty program for seniors featuring gentle treatments, age-appropriate skincare, and comfortable pampering sessions for mature clients.', 'date' => Carbon::now()->subDays(108)],
                ['name' => 'Express Beauty Boost', 'duration' => 30, 'benefits' => 'Quick treatments, lunch-hour friendly', 'price' => 69.99, 'description' => 'Fast-paced beauty treatments perfect for busy schedules featuring express facials, quick touch-ups, and efficient pampering during lunch breaks.', 'date' => Carbon::now()->subDays(102)],
                ['name' => 'Holistic Wellness Journey', 'duration' => 365, 'benefits' => 'Mind-body treatments, meditation', 'price' => 899.99, 'description' => 'Complete holistic approach to beauty and wellness combining traditional treatments with meditation, aromatherapy, and mind-body healing practices.', 'date' => Carbon::now()->subDays(96)],
                ['name' => 'Post-Surgery Recovery', 'duration' => 90, 'benefits' => 'Healing treatments, scar management', 'price' => 279.99, 'description' => 'Specialized recovery program for post-surgical clients featuring gentle healing treatments, scar management therapy, and restorative skincare protocols.', 'date' => Carbon::now()->subDays(90)],
                ['name' => 'Fitness Enthusiast Package', 'duration' => 120, 'benefits' => 'Sports massage, recovery treatments', 'price' => 219.99, 'description' => 'Active lifestyle support program featuring sports massage therapy, muscle recovery treatments, and skincare for fitness enthusiasts and athletes.', 'date' => Carbon::now()->subDays(84)],
                ['name' => 'Luxury Indulgence', 'duration' => 180, 'benefits' => 'Premium services, exclusive products', 'price' => 799.99, 'description' => 'Ultimate luxury experience featuring the finest treatments, exclusive premium products, and personalized service in our most opulent spa environment.', 'date' => Carbon::now()->subDays(78)],
                ['name' => 'Student Beauty Basics', 'duration' => 60, 'benefits' => 'Affordable treatments, education', 'price' => 59.99, 'description' => 'Budget-friendly beauty program for students featuring essential treatments, skincare education, and affordable maintenance routines for young adults.', 'date' => Carbon::now()->subDays(72)],
                ['name' => 'Weekend Warrior', 'duration' => 30, 'benefits' => 'Weekend availability, flexible scheduling', 'price' => 99.99, 'description' => 'Convenient weekend beauty package designed for busy professionals featuring flexible scheduling, weekend availability, and efficient treatment sessions.', 'date' => Carbon::now()->subDays(66)],
                ['name' => 'Stress Relief Sanctuary', 'duration' => 90, 'benefits' => 'Relaxation therapy, stress management', 'price' => 189.99, 'description' => 'Comprehensive stress relief program combining relaxation therapies, meditation sessions, and calming treatments for mental and physical rejuvenation.', 'date' => Carbon::now()->subDays(60)],
                ['name' => 'Skin Transformation', 'duration' => 180, 'benefits' => 'Advanced skincare, visible results', 'price' => 449.99, 'description' => 'Intensive skin transformation program featuring advanced treatments, medical-grade products, and progressive protocols for dramatic skin improvement.', 'date' => Carbon::now()->subDays(54)],
                ['name' => 'Monthly Maintenance', 'duration' => 30, 'benefits' => 'Regular upkeep, consistent care', 'price' => 79.99, 'description' => 'Consistent monthly beauty maintenance program ensuring regular upkeep, preventive care, and sustained results through scheduled treatment sessions.', 'date' => Carbon::now()->subDays(48)],
                ['name' => 'Special Occasion Prep', 'duration' => 14, 'benefits' => 'Event preparation, styling', 'price' => 149.99, 'description' => 'Intensive preparation package for special events featuring styling consultations, beauty treatments, and professional coordination for memorable occasions.', 'date' => Carbon::now()->subDays(42)],
                ['name' => 'Corporate Wellness', 'duration' => 365, 'benefits' => 'Group rates, employee benefits', 'price' => 599.99, 'description' => 'Corporate wellness program offering employee beauty benefits, group treatment rates, and workplace stress relief services for business organizations.', 'date' => Carbon::now()->subDays(36)],
                ['name' => 'Seasonal Detox', 'duration' => 21, 'benefits' => 'Detox treatments, cleansing protocols', 'price' => 129.99, 'description' => 'Intensive detoxification program featuring cleansing treatments, purifying protocols, and rejuvenating therapies for complete body and skin renewal.', 'date' => Carbon::now()->subDays(30)],
                ['name' => 'Beauty Maintenance Plus', 'duration' => 90, 'benefits' => 'Enhanced services, premium care', 'price' => 249.99, 'description' => 'Enhanced maintenance program offering premium care, advanced treatments, and comprehensive beauty services for sustained radiance and wellness.', 'date' => Carbon::now()->subDays(24)],
                ['name' => 'Quick Fix Solutions', 'duration' => 7, 'benefits' => 'Immediate results, fast treatments', 'price' => 89.99, 'description' => 'Rapid beauty solutions for immediate results featuring fast-acting treatments, quick fixes, and instant improvement protocols for urgent beauty needs.', 'date' => Carbon::now()->subDays(18)],
                ['name' => 'Platinum Elite Experience', 'duration' => 365, 'benefits' => 'All-inclusive, concierge service', 'price' => 1999.99, 'description' => 'Ultimate platinum membership offering all-inclusive services, personal concierge assistance, and exclusive access to premium treatments and facilities.', 'date' => Carbon::now()->subDays(12)],
                ['name' => 'Recovery & Renewal', 'duration' => 60, 'benefits' => 'Healing focus, gentle restoration', 'price' => 169.99, 'description' => 'Gentle recovery program focusing on healing and restoration featuring therapeutic treatments, calming protocols, and restorative beauty care.', 'date' => Carbon::now()->subDays(6)]
            ];

            foreach ($membershipRecords as $record) {
                // Select random service for membership
                $selectedService = $services->random();

                BeautyMembership::create([
                    'name' => $record['name'],
                    'duration' => $record['duration'],
                    'benefits' => $record['benefits'],
                    'price' => $record['price'],
                    'description' => $record['description'],
                    'included_services_id' => $selectedService->id,
                    'creator_id' => $userId,
                    'created_by' => $userId,
                    'created_at' => $record['date'],
                    'updated_at' => $record['date'],
                ]);
            }
        }
    }
}