<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyReview;
use Workdo\BeautySpaManagement\Models\BeautyService;
use Carbon\Carbon;

class DemoBeautyReviewSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyReview::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            // Get existing beauty services to link reviews
            $services = BeautyService::where('created_by', $userId)->get();
            
            if ($services->isEmpty()) {
                return; // No services available to link reviews
            }

            // 20 realistic review records ordered oldest to newest (6 months)
            $reviewRecords = [
                ['name' => 'Sarah Mitchell', 'email' => 'sarah.mitchell@gmail.com', 'rating' => 5, 'review' => 'Absolutely amazing experience! The facial treatment was incredibly relaxing and my skin looks radiant. The staff was professional and knowledgeable. Will definitely be returning for more treatments.', 'date' => Carbon::now()->subDays(180)],
                ['name' => 'Jessica Thompson', 'email' => 'jessica.thompson@yahoo.com', 'rating' => 4, 'review' => 'Great massage therapy session. The therapist was skilled and the atmosphere was very calming. Only minor issue was the waiting time, but overall excellent service and results.', 'date' => Carbon::now()->subDays(171)],
                ['name' => 'Amanda Foster', 'email' => 'amanda.foster@hotmail.com', 'rating' => 5, 'review' => 'The bridal package exceeded my expectations! Every detail was perfect from the trial session to the wedding day. My makeup and hair looked flawless and lasted all day. Highly recommended!', 'date' => Carbon::now()->subDays(162)],
                ['name' => 'Lisa Rodriguez', 'email' => 'lisa.rodriguez@outlook.com', 'rating' => 3, 'review' => 'The service was okay but not exceptional. The treatment room was clean and staff was friendly, but I expected more personalized attention for the price. Results were average.', 'date' => Carbon::now()->subDays(153)],
                ['name' => 'Maria Garcia', 'email' => 'maria.garcia@gmail.com', 'rating' => 5, 'review' => 'Outstanding anti-aging treatment! I can see visible improvement in my skin texture and fine lines. The aesthetician explained everything clearly and provided excellent aftercare instructions. Worth every penny!', 'date' => Carbon::now()->subDays(144)],
                ['name' => 'Rachel Green', 'email' => 'rachel.green@yahoo.com', 'rating' => 4, 'review' => 'Lovely spa experience with my sister. The couples package was well-organized and we both enjoyed our treatments. The relaxation area was beautiful and peaceful. Great value for money.', 'date' => Carbon::now()->subDays(135)],
                ['name' => 'Jennifer Lee', 'email' => 'jennifer.lee@gmail.com', 'rating' => 5, 'review' => 'The chemical peel treatment was exactly what my skin needed. Professional consultation beforehand helped set proper expectations. Healing process went smoothly and results are fantastic. Thank you!', 'date' => Carbon::now()->subDays(126)],
                ['name' => 'Ashley Wilson', 'email' => 'ashley.wilson@hotmail.com', 'rating' => 2, 'review' => 'Unfortunately, the service did not meet my expectations. The treatment felt rushed and the results were not as promised. Staff seemed inexperienced and I left feeling disappointed with the overall experience.', 'date' => Carbon::now()->subDays(117)],
                ['name' => 'Nicole Brown', 'email' => 'nicole.brown@outlook.com', 'rating' => 4, 'review' => 'Good experience overall. The nail technician was skilled and my manicure lasted longer than expected. Clean facility and professional service. Would consider returning for other treatments.', 'date' => Carbon::now()->subDays(108)],
                ['name' => 'Stephanie Davis', 'email' => 'stephanie.davis@gmail.com', 'rating' => 5, 'review' => 'Exceptional service from start to finish! The consultation was thorough, treatment was relaxing, and results exceeded expectations. The entire team made me feel pampered and valued as a client.', 'date' => Carbon::now()->subDays(99)],
                ['name' => 'Megan Taylor', 'email' => 'megan.taylor@yahoo.com', 'rating' => 4, 'review' => 'Very pleased with the body wrap treatment. The process was explained clearly and I felt comfortable throughout. Skin feels incredibly soft and smooth. Booking process was easy and convenient.', 'date' => Carbon::now()->subDays(90)],
                ['name' => 'Kimberly White', 'email' => 'kimberly.white@gmail.com', 'rating' => 5, 'review' => 'The microdermabrasion treatment was amazing! My skin looks brighter and feels smoother than it has in years. The aesthetician was knowledgeable and provided great skincare advice for home care.', 'date' => Carbon::now()->subDays(81)],
                ['name' => 'Danielle Harris', 'email' => 'danielle.harris@hotmail.com', 'rating' => 3, 'review' => 'Decent service but room for improvement. The treatment was effective but the ambiance could be more relaxing. Staff was professional but seemed busy. Results were good though.', 'date' => Carbon::now()->subDays(72)],
                ['name' => 'Brittany Clark', 'email' => 'brittany.clark@outlook.com', 'rating' => 5, 'review' => 'Incredible hot stone massage! The therapist was highly skilled and the heated stones provided deep relaxation. Perfect pressure and technique. This is now my go-to spa for stress relief.', 'date' => Carbon::now()->subDays(63)],
                ['name' => 'Samantha Lewis', 'email' => 'samantha.lewis@gmail.com', 'rating' => 4, 'review' => 'Great experience with the oxygen facial. My skin immediately looked more radiant and felt refreshed. The treatment was gentle yet effective. Booking was easy and staff was accommodating.', 'date' => Carbon::now()->subDays(54)],
                ['name' => 'Christina Walker', 'email' => 'christina.walker@yahoo.com', 'rating' => 5, 'review' => 'Outstanding service! The full spa day package was worth every dollar. Multiple treatments were seamlessly coordinated and I felt completely pampered. Facilities are beautiful and staff is exceptional.', 'date' => Carbon::now()->subDays(45)],
                ['name' => 'Rebecca Hall', 'email' => 'rebecca.hall@gmail.com', 'rating' => 4, 'review' => 'Excellent hair styling service for my daughter\'s prom. The stylist listened to our ideas and created a beautiful updo that lasted all night. Professional service and reasonable pricing.', 'date' => Carbon::now()->subDays(36)],
                ['name' => 'Laura Allen', 'email' => 'laura.allen@hotmail.com', 'rating' => 5, 'review' => 'The aromatherapy session was exactly what I needed for stress relief. The essential oils were divine and the massage technique was perfect. Left feeling completely relaxed and rejuvenated.', 'date' => Carbon::now()->subDays(27)],
                ['name' => 'Melissa Young', 'email' => 'melissa.young@outlook.com', 'rating' => 4, 'review' => 'Very satisfied with the skincare consultation. The aesthetician provided detailed analysis and personalized product recommendations. My skin has improved significantly following their advice. Professional and knowledgeable staff.', 'date' => Carbon::now()->subDays(18)],
                ['name' => 'Victoria King', 'email' => 'victoria.king@gmail.com', 'rating' => 5, 'review' => 'Perfect experience from booking to treatment completion. The executive grooming package was ideal for my busy schedule. Quick, efficient, and professional service with excellent results. Highly recommend for professionals.', 'date' => Carbon::now()->subDays(9)]
            ];

            foreach ($reviewRecords as $record) {
                // Select random service for review
                $selectedService = $services->random();

                BeautyReview::create([
                    'name' => $record['name'],
                    'email' => $record['email'],
                    'beauty_services_id' => $selectedService->id,
                    'rating' => $record['rating'],
                    'review' => $record['review'],
                    'creator_id' => $userId,
                    'created_by' => $userId,
                    'created_at' => $record['date'],
                    'updated_at' => $record['date'],
                ]);
            }
        }
    }
}