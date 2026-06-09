<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\PhotoStudioManagement\Models\PhotoStudioSetup;
use Workdo\PhotoStudioManagement\Models\PhotoStudioGalleryType;

class DemoPhotoStudioSetupSeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioSetup::where('created_by', $userId)->exists()) {
            return;
        }

        $galleryTypes = PhotoStudioGalleryType::where('created_by', $userId)->get();

        if ($galleryTypes->isEmpty()) {
            return;
        }

        $setupData = [
            'site_title'         => 'Vistara Photo Studio',
            'footer_text'        => '© 2025 Vistara All rights reserved.',
            'footer_description' => 'Professional photography studio offering premium services for portraits, events, and commercial shoots with state-of-the-art equipment.',

            'logo'        => 'photostudio-logo.png',
            'footer_logo' => 'photostudio-footer-logo.png',
            'favicon'     => 'photostudio-favicon.png',

            'copy_link_card_title'       => 'Welcome to Photo Studio Management',
            'copy_link_card_description' => 'Share your photo studio portal with clients and customers.',
            'copy_link_button_text'      => 'Copy Link',
            'copy_link_button_icon'      => 'Copy',

            'banner_section' => json_encode([
                'banners' => [
                    [
                        'title'       => 'Capturing Life’s Perfect Moments',
                        'sub_title'   => 'Your Story, Our Lens',
                        'image'       => 'photostudio-hero-banner1.png',
                        'description' => 'Professional photography studio specializing in portraits, weddings, commercial, and creative shoots. Experience artistry, passion, and technical excellence with every click, capturing  memories that last forever.',
                    ],
                    [
                        'title'       => 'Your Love Story, Beautifully Captured',
                        'sub_title'   => 'Award-Winning Wedding Photography',
                        'image'       => 'photostudio-hero-banner2.png',
                        'description' => 'From engagement to "I do," our expert team preserves every magical moment with artistry, passion, and care. Trust us to make your wedding memories last a lifetime, beautifully and forever cherished.',
                    ],
                    [
                        'title'       => 'Elevate Your Brand With Stunning Visuals',
                        'sub_title'   => 'Creative Commercial Shoots',
                        'image'       => 'photostudio-hero-banner3.png',
                        'description' => 'We help businesses stand out with high-impact product, lifestyle, and branding photography. Let’s create images that drive results for your business, boost engagement, and elevate your brand identity.',
                    ],
                ],
            ]),

            'about_section' => json_encode([
                'title'          => 'About Our Work',
                'sub_title'      => 'Who We Are',
                'content'        => 'We are a team of passionate photographers dedicated to capturing your most meaningful moments with clarity, artistry, and care. With over 15 years of experience, we deliver timeless images for weddings, portraits, events, and brands.',
                'description'    => 'Our mission is to turn stories into images. With a creative eye and commitment, we bring your vision to life one photograph at a time.',
                'about_us_image' => 'photostudio-about-image.png',
                'tips' => [
                    ['description' => 'Award-winning team of creative professionals'],
                    ['description' => 'State of the art studio and equipment'],
                    ['description' => 'Personalized approach for every client'],
                    ['description' => 'Trusted by 500+ happy clients'],
                ],
            ]),

            'title_section' => json_encode([
                'service_page_title'       => 'Our Services',
                'service_label'            => 'Our Services',
                'service_title'            => 'Professional Photography Services',
                'camera_kit_page_title'    => 'Camera Equipment',
                'camera_kit_label'         => 'Professional Equipment',
                'camera_kit_title'         => 'Our Camera Kit',
                'camera_kit_details_label' => 'Professional Equipment',
                'camera_kit_details_title' => 'Our Equipment Arsenal',
                'equipment_label'          => 'Equipment Specs',
                'equipment_title'          => 'Complete Equipment Specifications',
                'booking_page_title'       => 'Book Appointment',
            ]),

            'gallery_section' => json_encode([
                'gallery_page_title'     => 'Our Portfolio',
                'gallery_label'          => 'Featured Gallery',
                'gallery_title'          => 'A Showcase of Our Finest Work',
                'gallery_category_label' => 'Browse By Category',
                'gallery_category_title' => 'Our Photography Work',
                'images' => [
                    ['image' => 'photostudio-gallery-1.png', 'gallery_type_id' => (string) $galleryTypes->random()->id],
                    ['image' => 'photostudio-gallery-2.png', 'gallery_type_id' => (string) $galleryTypes->random()->id],
                    ['image' => 'photostudio-gallery-3.png', 'gallery_type_id' => (string) $galleryTypes->random()->id],
                    ['image' => 'photostudio-gallery-4.png', 'gallery_type_id' => (string) $galleryTypes->random()->id],
                    ['image' => 'photostudio-gallery-5.png', 'gallery_type_id' => (string) $galleryTypes->random()->id],
                    ['image' => 'photostudio-gallery-6.png', 'gallery_type_id' => (string) $galleryTypes->random()->id],
                ],
            ]),

            'testimonials' => json_encode([
                'client_feedback_label' => 'Client Feedback',
                'client_feedback_title' => 'Award-Winning Results',
                'testimonial_title'     => 'Need help with professional photography? Let`s work together!',
                'testimonial_image'     => 'photostudio-testimonial-bg.png',
                'testimonials' => [
                    [
                        'customer_name' => 'Jason Brown',
                        'designation'   => 'Project Owner',
                        'rating'        => 5,
                        'comment'       => 'Vistara captured our wedding day perfectly. Every photo tells a beautiful story. We are happier with the results!',
                        'profile_image' => 'photostudio-client-1.png',
                    ],
                    [
                        'customer_name' => 'Emily Johnson',
                        'designation'   => 'Wedding Client',
                        'rating'        => 4,
                        'comment'       => 'Outstanding commercial photography for our brand. Professional team, quick turnaround, and exceptional quality.',
                        'profile_image' => 'photostudio-client-2.png',
                    ],
                    [
                        'customer_name' => 'Michael Chen',
                        'designation'   => 'Marketing Director',
                        'rating'        => 3,
                        'comment'       => 'The portrait session was amazing. The photographers made me feel comfortable and the photos are absolutely stunning.',
                        'profile_image' => 'photostudio-client-3.png',
                    ],
                ],
            ]),

            'award_section' => json_encode([
                'award_page_title' => 'Awards & Media',
                'label'            => 'Excellence Recognized',
                'title'            => 'Awards & Recognition',
                'awards' => [
                    [
                        'award_title'      => 'Professional Photographer of the Year',
                        'award_name'       => 'International Photography Awards',
                        'award_icon'       => 'Trophy',
                        'description'      => 'Recognized for exceptional artistic vision and technical excellence in portraitphotography, showcasing innovative lighting techniques and compelling compositions.',
                        'achievement_name' => 'Gold Medal Winner',
                        'achievement_icon' => 'Camera',
                    ],
                    [
                        'award_title'      => 'Best Wedding Photographer',
                        'award_name'       => 'WeddingWire Couple Choice Awards',
                        'award_icon'       => 'Star',
                        'description'      => 'Awarded for consistently delivering exceptional wedding photography services with over 98% customer satisfaction rating and numerous five-star reviews.',
                        'achievement_name' => 'Hall of Fame Inductee',
                        'achievement_icon' => 'Medal',
                    ],
                    [
                        'award_title'      => 'Innovation in Photography',
                        'award_name'       => 'Photography Masters Cup',
                        'award_icon'       => 'Award',
                        'description'      => 'Recognized for innovative use of technology and creative lighting techniques in commercial photography, setting new industry standards.',
                        'achievement_name' => 'Innovation Excellence',
                        'achievement_icon' => 'Briefcase',
                    ],
                    [
                        'award_title'      => 'Business Excellence Award',
                        'award_name'       => 'Local Chamber of Commerce',
                        'award_icon'       => 'Award',
                        'description'      => 'Honored for outstanding business practices, community involvement, and contribution to the local arts and photography community.',
                        'achievement_name' => 'Community Leader',
                        'achievement_icon' => 'Trophy',
                    ],
                ],
            ]),

            'media_section' => json_encode([
                'label' => 'In The Press',
                'title' => 'Media Coverage',
                'media_items' => [
                    [
                        'media_heading' => '"Rising Stars in Portrait Photography"',
                        'media_image'   => 'photostudio-media-1.png',
                        'date'          => '2024-03-15',
                        'content_type'  => 'Cover Story',
                        'content'       => 'Featured as one of the top emerging photographers in the region, highlighting innovative techniques and exceptional client satisfaction.',
                    ],
                    [
                        'media_heading' => '"Master of Wedding Photography"',
                        'media_image'   => 'photostudio-media-2.png',
                        'date'          => '2024-05-20',
                        'content_type'  => 'Expert Interview',
                        'content'       => 'Comprehensive interview discussing wedding photography trends, techniques, and the importance of capturing authentic moments.',
                    ],
                    [
                        'media_heading' => '"Inspiring Local Small Business Success Story"',
                        'media_image'   => 'photostudio-media-3.png',
                        'date'          => '2024-07-10',
                        'content_type'  => 'Business Profile',
                        'content'       => 'Feature story on building a successful photography business, focusing on client relationships and quality service delivery.',
                    ],
                    [
                        'media_heading' => '"Artistic Vision in Commercial Work"',
                        'media_image'   => 'photostudio-media-4.png',
                        'date'          => '2024-09-05',
                        'content_type'  => 'Art Review',
                        'content'       => 'Analysis of how artistic photography principles can enhance commercial and corporate photography projects.',
                    ],
                ],
            ]),

            'faq_section' => json_encode([
                'faq_page_title' => 'Frequently Asked Questions',
                'faq_label'      => 'Get Answers',
                'faq_title'      => 'Photography Services FAQ',
                'faqs' => [
                    [
                        'question' => 'How do I book a photography session?',
                        'answer'   => 'You can book a session through our online booking form, by calling us directly, or by visiting our studio. We recommend booking at least 2 weeks in advance.',
                    ],
                    [
                        'question' => 'What should I wear for a portrait session?',
                        'answer'   => 'We recommend wearing solid colors and avoiding busy patterns. Bring 2-3 outfit options. Our team will guide you on the best choices for your session.',
                    ],
                    [
                        'question' => 'How long does it take to receive my photos?',
                        'answer'   => 'Standard delivery is 7-10 business days. Rush delivery (3-5 days) is available for an additional fee. Wedding photos typically take 3-4 weeks.',
                    ],
                    [
                        'question' => 'Do you offer outdoor photography sessions?',
                        'answer'   => 'Yes! We offer both studio and outdoor sessions. We have several preferred outdoor locations and can also shoot at your chosen location.',
                    ],
                    [
                        'question' => 'What is your cancellation policy?',
                        'answer'   => 'Cancellations made 48+ hours in advance receive a full refund. Cancellations within 48 hours may be subject to a cancellation fee. Rescheduling is always welcome.',
                    ],
                    [
                        'question' => 'Do you provide photo editing and retouching?',
                        'answer'   => 'Yes, all our packages include professional editing and retouching. We enhance lighting, color, and clarity to ensure every image looks its absolute best.',
                    ],
                    [
                        'question' => 'Can I request a specific photographer?',
                        'answer'   => 'Absolutely! You can request a specific photographer when booking, subject to availability. We recommend booking early to secure your preferred photographer.',
                    ],
                    [
                        'question' => 'What file formats will I receive my photos in?',
                        'answer'   => 'You will receive high-resolution JPEG files delivered via a private online gallery. RAW files are available upon request for an additional fee.',
                    ],
                    [
                        'question' => 'Do you offer group or family photography packages?',
                        'answer'   => 'Yes, we offer tailored packages for groups, families, and corporate teams. Contact us to discuss your requirements and we will create a custom package for you.',
                    ],
                    [
                        'question' => 'Is a deposit required to confirm a booking?',
                        'answer'   => 'Yes, a 30% deposit is required to confirm your booking. The remaining balance is due on the day of the session. We accept all major payment methods.',
                    ],
                ],
            ]),

            'contact_section' => json_encode([
                'contact_page_title' => 'Contact Us',
                'location_title'     => 'Our Location',
                'contact_title'      => 'Call Us',
                'email_title'        => 'Email Us',
                'visit_address'      => '123 Photography Lane Studio City, CA 90210',
                'call_details'       => '+1 (555) 123-4567 <br>Mon-Fri: 9AM-6PM</br>',
                'support_email'      => 'info@vistara.com <br>We respond within 24hrs</br>',
                'location_icon'      => 'MapPin',
                'contact_icon'       => 'Phone',
                'email_icon'         => 'Mail',
                'google_map_iframe'  => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.215!2d-74.0060!3d40.7128!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1635959542207!5m2!1sen!2s" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
            ]),

            'footer_section' => json_encode([
                'location'         => '123 Photography Lane Studio City, CA 90210',
                'phone_no'         => '+1 (555) 123-4567',
                'email'            => 'info@vistara.com',
                'location_icon'    => 'MapPin',
                'phone_icon'       => 'Phone',
                'email_icon'       => 'Mail',
                'newsletter_label' => 'Sign up to get latest update',
                'newsletter_title' => 'Sign up for our monthly newsletter for the latest news & articles',
                'social_links' => [
                    ['social_link' => 'https://www.facebook.com/',  'social_icon' => 'Facebook'],
                    ['social_link' => 'https://www.instagram.com/', 'social_icon' => 'Instagram'],
                    ['social_link' => 'https://www.twitter.com/',   'social_icon' => 'X'],
                    ['social_link' => 'https://www.youtube.com/',   'social_icon' => 'Youtube'],
                ],
            ]),
        ];

        foreach ($setupData as $key => $value) {
            PhotoStudioSetup::create([
                'key'        => $key,
                'value'      => $value,
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }
}
