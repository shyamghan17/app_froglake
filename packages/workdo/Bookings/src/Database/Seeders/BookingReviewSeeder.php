<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\Bookings\Models\BookingReview;
use Workdo\ProductService\Models\ProductServiceItem;
use App\Models\User;
use Carbon\Carbon;

class BookingReviewSeeder extends Seeder
{
    public function run($userId)
    {
        if (!empty($userId)) {
            $this->createReviewsForUser($userId);
        }
    }
    
    private function createReviewsForUser($userId)
    {
        if (BookingReview::where('created_by', $userId)->exists()) {
            return;
        }
        
        $items = ProductServiceItem::where('created_by', $userId)
            ->where('type', 'bookings')
            ->pluck('id')
            ->toArray();
            
        if (empty($items)) return;
        
        $reviewData = [
            ['name' => 'Emma Johnson', 'email' => 'emma.johnson@example.com', 'rating' => 5, 'comment' => 'Absolutely amazing service! Sarah did an incredible job with my hair cut and styling. The salon is clean and professional.'],
            ['name' => 'Sophia Williams', 'email' => 'sophia.williams@example.com', 'rating' => 5, 'comment' => 'Best facial treatment I have ever received! My skin feels so refreshed and glowing. Jessica is very skilled.'],
            ['name' => 'Olivia Brown', 'email' => 'olivia.brown@example.com', 'rating' => 4, 'comment' => 'Great massage therapy session. Amanda really knows how to work out the tension. Very relaxing experience.'],
            ['name' => 'Ava Davis', 'email' => 'ava.davis@example.com', 'rating' => 5, 'comment' => 'Perfect manicure and pedicure! Emily is so detail-oriented and the results lasted for weeks. Highly recommend!'],
            ['name' => 'Isabella Miller', 'email' => 'isabella.miller@example.com', 'rating' => 5, 'comment' => 'Amazing eyebrow threading service. Rachel is very precise and professional. Love the shape she created!'],
            ['name' => 'Mia Wilson', 'email' => 'mia.wilson@example.com', 'rating' => 4, 'comment' => 'Beautiful makeup application for my wedding trial. Lisa understood exactly what I wanted. Can\'t wait for the actual day!'],
            ['name' => 'Charlotte Moore', 'email' => 'charlotte.moore@example.com', 'rating' => 5, 'comment' => 'Deep tissue massage was exactly what I needed. Maria has magic hands and really helped with my back pain.'],
            ['name' => 'Amelia Taylor', 'email' => 'amelia.taylor@example.com', 'rating' => 4, 'comment' => 'Lovely bridal makeup session. Ashley made me feel so beautiful and confident. Great attention to detail.'],
            ['name' => 'Harper Anderson', 'email' => 'harper.anderson@example.com', 'rating' => 5, 'comment' => 'Hair coloring turned out perfect! Nicole really listened to what I wanted and delivered amazing results.'],
            ['name' => 'Evelyn Thomas', 'email' => 'evelyn.thomas@example.com', 'rating' => 4, 'comment' => 'Acne treatment facial was very effective. Stephanie explained everything and my skin has improved significantly.'],
            ['name' => 'Abigail Jackson', 'email' => 'abigail.jackson@example.com', 'rating' => 5, 'comment' => 'Gel nail extensions look fantastic! Jennifer is an artist with nails. The design is exactly what I imagined.'],
            ['name' => 'Emily White', 'email' => 'emily.white@example.com', 'rating' => 5, 'comment' => 'Aromatherapy massage was so relaxing. Michelle created the perfect atmosphere and I felt completely rejuvenated.'],
            ['name' => 'Elizabeth Harris', 'email' => 'elizabeth.harris@example.com', 'rating' => 4, 'comment' => 'Microblading results exceeded my expectations. The healing process was smooth and the shape is perfect.'],
            ['name' => 'Sofia Martin', 'email' => 'sofia.martin@example.com', 'rating' => 5, 'comment' => 'Keratin treatment made my hair so smooth and manageable. No more frizz! The process was comfortable too.'],
            ['name' => 'Avery Thompson', 'email' => 'avery.thompson@example.com', 'rating' => 5, 'comment' => 'Anti-aging facial was worth every penny. My skin looks years younger and feels so soft. Will definitely return!']
        ];
        
        foreach ($reviewData as $review) {
            BookingReview::create(array_merge($review, [
                'item_id' => $items[array_rand($items)],
                'created_by' => $userId,
                'creator_id' => $userId,
                'created_at' => Carbon::now()->subDays(rand(0, 180)),
            ]));
        }
    }
}