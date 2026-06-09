<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautySetup;

class DemoBeautySetupSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautySetup::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            $setupData = [
                // Brand Settings
                'logo' => 'beauty-logo.png',
                'favicon' => 'beauty-favicon.png',
                'footer_text' => 'Serenity Spa.All rights reserved.',
                'footer_description' => 'Experience the ultimate in relaxation and rejuvenation.',
                'beauty_spa_store_name' => 'Beauty Saloon',

                // Banner Section
                'banner_section' => json_encode([
                    'heading' => 'Premium Beauty Experience',
                    'title' => 'Discover Your Natural Beauty',
                    'description' => 'Indulge in our luxurious spa treatments designed to rejuvenate your body and mind, providing the ultimate relaxation experience.',
                    'image' => 'beauty-banner-image.png'
                ]),

                // Home Section
                'home_section' => json_encode([
                    'services_title' => 'Our Premium Services',
                    'services_description' => 'Indulge in our carefully curated selection of beauty and wellness treatments, each designed to rejuvenate your body and spirit.',
                    'offers_title' => 'Special Offers',
                    'offers_description' => 'Take advantage of our exclusive packages and seasonal promotions for the ultimate spa experience.'
                ]),

                // Feature Section
                'feature_section' => json_encode([
                    'why_choose_us_title' => 'Why Choose Our Spa',
                    'why_choose_us_description' => 'Experience excellence in beauty and wellness with our professional services and luxurious facilities.',
                    'features' => [
                        [
                            'title' => 'Expert Professionals',
                            'description' => 'Certified beauty specialists with years of experience',
                            'icon' => 'Search'
                        ],
                        [
                            'title' => 'Natural Products',
                            'description' => 'Premium organic and natural products for safe and effective treatments',
                            'icon' => 'Leaf'
                        ],
                        [
                            'title' => 'Award Winning',
                            'description' => 'Recognized for excellence in spa services and customer satisfaction',
                            'icon' => 'Award'
                        ]
                    ]
                ]),

                // About Section
                'about_section' => json_encode([
                    'about_image' => 'beauty-about-img.jpg',
                    'main_title' => 'About Serenity Beauty Spa',
                    'content' => '<p>I have dedicated years understanding the unique needs of each client. I specialize in a wide range of massage techniques from Swedish and deep tissue to aromatherapy and reflexology allowing me to tailor each session to deliver maximum relief and relaxation.</p><p></p><p>In addition to offering premium services, in this we operate as a trusted wholesaler and distributor of spa and wellness products. Your satisfaction is our commitment, and we\'re here to help you thrive beautifully and naturally.</p><p></p><p>Whether you\'re a retail client or a business partner, you can rely on us for consistent value, transparent pricing, and unmatched product selection. Your satisfaction is our commitment, and we\'re here to help you thrive beautifully and naturally.</p>',
                    'sub_text' => 'Your trusted partner in beauty and wellness transformation.',
                    'purpose_title' => 'Our Mission & Vision',
                    'purpose_description' => 'To provide transformative beauty experiences that inspire confidence and promote wellness while being the leading destination for luxury beauty services.',
                    'about_stats' => [
                        ['title' => 'Years Experience', 'description' => '10+ years of professional beauty services', 'icon' => 'Calendar'],
                        ['title' => 'Happy Clients', 'description' => '5000+ satisfied customers worldwide', 'icon' => 'Users'],
                    ]
                ]),

                // Contact Info
                'contact_info' => json_encode([
                    'header_title' => 'Get In Touch',
                    'header_description' => 'Contact us today to book your appointment or learn more about our services.',
                    'location' => '123 Serenity Lane, Wellness City, CA 90210',
                    'phone_number' => '+15551234568',
                    'email_address' => 'info@serenityspa.com',
                    'location_icon' => 'MapPin',
                    'phone_icon' => 'Phone',
                    'email_icon' => 'Mail',
                    'map_title' => 'Find Us Here',
                    'map_subtext' => 'Visit our beautiful spa location',
                    'map_iframe' => '<iframe  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.30596598663!2d-74.25986581051912!3d40.69714941664915!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sca!4v1652810855852!5m2!1sen!2sca"  width="100%"   height="450" style="border:0;" allowfullscreen="" loading="lazy"  referrerpolicy="no-referrer-when-downgrade"></iframe>',
                    'follow_us_description' => 'Follow us on social media for the latest updates and beauty tips.',
                    'cta_title' => 'Ready to Experience Serenity?',
                    'cta_description' => 'Book your appointment today and treat yourself to the relaxation and rejuvenation you deserve.'
                ]),

                // Social Links
                'social_links' => json_encode([
                    'social_links' => [
                        ['url' => 'https://facebook.com/serenitybeautyspa', 'icon' => 'Facebook'],
                        ['url' => 'https://instagram.com/serenitybeautyspa', 'icon' => 'Instagram'],
                        ['url' => 'https://youtube.com/serenitybeautyspa', 'icon' => 'Youtube'],
                        ['url' => 'https://wa.me/15551234568', 'icon' => 'svg:whatsapp']
                    ]
                ]),

                // Testimonials
                'testimonials' => json_encode([
                    'title' => 'What Our Clients Say',
                    'description' => 'Read testimonials from our satisfied clients who have experienced our exceptional beauty services.',
                    'testimonials' => [
                        [
                            'customer_name' => 'Sarah Johnson',
                            'rating' => 5,
                            'comment' => 'Absolutely amazing experience! The staff is professional and the treatments are top-notch.',
                            'designation' => 'Regular Client'
                        ],
                        [
                            'customer_name' => 'Emily Davis',
                            'rating' => 5,
                            'comment' => 'Best spa in town! I always leave feeling refreshed and beautiful.',
                            'designation' => 'VIP Member'
                        ],
                        [
                            'customer_name' => 'Jessica Wilson',
                            'rating' => 5,
                            'comment' => 'The bridal package was perfect for my wedding day. Highly recommended!',
                            'designation' => 'Bride'
                        ]
                    ]
                ])
            ];

            foreach ($setupData as $key => $value) {
                BeautySetup::updateOrCreate(
                    ['key' => $key, 'created_by' => $userId],
                    [
                        'value' => $value,
                        'creator_id' => $userId,
                    ]
                );
            }
        }
    }
}