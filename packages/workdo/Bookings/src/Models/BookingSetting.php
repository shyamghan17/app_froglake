<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSetting extends Model
{
    protected $fillable = [
        'config_data',
        'created_by'
    ];

    protected $casts = [
        'config_data' => 'array'
    ];
    
    public static function getDefaultConfig()
    {
        $user = auth()->user();
        return [
            'general' => [
                'header' => [
                    'logo' => 'packages/workdo/Bookings/src/assets/images/header-log.png',
                    'favicon' => 'packages/workdo/Bookings/src/assets/images/favicon.png',
                    'site_title' => 'Service Bookings Addon',
                    'show_language_selector' => true,
                    'default_language' => 'en',
                    'navigation_items' => [
                        ['text' => 'Services', 'href' => $user?->slug ? route('booking.services', ['userSlug' => $user->slug]) : null, 'type' => 'link', 'target' => '_self'],
                        ['text' => 'Contact Us', 'href' => $user?->slug ? route('booking.contact', ['userSlug' => $user->slug]) : null, 'type' => 'link', 'target' => '_self'],
                        ['text' => 'About Us', 'href' => $user?->slug ? route('booking.about', ['userSlug' => $user->slug]) : null, 'type' => 'link', 'target' => '_self']
                    ]
                ],
                'footer' => [
                    'logo' => 'packages/workdo/Bookings/src/assets/images/footer-logo.png',
                    'description' => 'Streamline your service booking process with our powerful, customizable booking addon solution. Perfect for businesses of all sizes.',
                    'contact_title' => 'Contact Information',
                    'address' => '123 Booking Street, Suite 101, New York, NY 10001',
                    'phone' => '+1 (555) 123-4567',
                    'email' => 'info@servicebooking.com',
                    'hours' => 'Mon-Fri: 9:00 AM - 6:00 PM',
                    'copyright' => '© 2025 Booking Addon. All rights reserved.',
                    'navigation_sections' => [
                        [
                            'title' => 'Quick Links',
                            'links' => [
                                ['text' => 'Home', 'href' => $user?->slug ? route('booking.home', ['userSlug' => $user->slug]) : null, 'type' => 'link', 'target' => '_self'],
                                ['text' => 'Services', 'href' => $user?->slug ? route('booking.services', ['userSlug' => $user->slug]) : null, 'type' => 'link', 'target' => '_self'],
                                ['text' => 'Contact Us', 'href' => $user?->slug ? route('booking.contact', ['userSlug' => $user->slug]) : null, 'type' => 'link', 'target' => '_self']
                            ]
                        ],
                        [
                            'title' => 'Our Services',
                            'links' => [
                                ['text' => 'Salon Services', 'href' => $user?->slug ? route('booking.services', ['category' => 'salon', 'userSlug' => $user->slug]) : null, 'type' => 'link', 'target' => '_self'],
                                ['text' => 'Spa Treatments', 'href' => $user?->slug ? route('booking.services', ['category' => 'spa', 'userSlug' => $user->slug]) : null, 'type' => 'link', 'target' => '_self'],
                                ['text' => 'Medical Consultations', 'href' => $user?->slug ? route('booking.services', ['category' => 'medical', 'userSlug' => $user->slug]) : null, 'type' => 'link', 'target' => '_self']
                            ]
                        ]
                    ]
                ],
                'colors' => [
                    'primary_color' => '#52816D',
                    'secondary_color' => '#ffffff'
                ]
            ],

            'pages' => [
                'home' => [
                    'hero' => [
                        'title' => 'Modern Service Booking',
                        'subtitle' => 'Service Booking',
                        'description' => 'Connect with expert professionals and book appointments seamlessly. Your perfect service experience starts here.',
                        'image' => '',
                        'button_text' => 'Book Now',
                        'button_url' => '/booking',
                        'button_text_two' => 'Explore Services',
                        'button_url_two' => '#services'
                    ],
                    'booking' => [
                        'title' => 'Book Your Service Online',
                        'description' => 'Schedule your appointment in minutes with our easy-to-use booking system. Select your preferred service, date, and time for a seamless booking experience.',
                        'subtitle' => '',
                        'icon' => 'Calendar',
                        'button_text' => 'Continue to Details',
                        'form_title' => 'Pick Your Date & Time',
                        'steps_title' => 'Easy Booking Process',
                        'features_title' => 'Why Book With Us?',
                        'form' => [
                            'details_title' => 'Your Details',
                            'appointment_summary_title' => 'Your Appointment',
                            'online_payment_text' => 'Online Payment',
                            'offline_payment_text' => 'Pay on Service',
                            'confirm_button_text' => 'Confirm Booking',
                            'confirmation_title' => 'Booking Confirmed!',
                            'confirmation_message' => 'Your appointment has been successfully scheduled.',
                            'confirmation_details_title' => 'Appointment Details',
                            'confirmation_email_message' => 'A confirmation has been sent to your email with all the details.',
                            'return_home_button' => 'Return to Home',
                        ],
                        'steps' => [
                            ['title' => 'Select Service & Time', 'description' => 'Choose from our range of services and pick your preferred time slot'],
                            ['title' => 'Provide Details', 'description' => 'Fill in your contact information and any special requirements'],
                            ['title' => 'Confirm Booking', 'description' => 'Review your booking details and confirm your appointment']
                        ],
                        'features' => [
                            ['text' => 'Certified professionals for every service'],
                            ['text' => 'Flexible scheduling to fit your busy lifestyle'],
                            ['text' => 'Secure payment processing and data protection']
                        ]
                    ],
                    'stats' => [
                        'title' => 'Trusted by Thousands Worldwide',
                        'description' => 'See why our booking solution has become the industry standard for service businesses',
                        'stats' => [
                            ['number' => '5000+','icon' => 'Calendar', 'label' => 'Daily Bookings', 'description' => 'Helping businesses manage thousands of appointments every day'],
                            ['number' => '1200', 'icon' => 'Users', 'label' => 'Active Businesses', 'description' => 'Companies across various industries rely on our solution'],
                            ['number' => '300+', 'icon' => 'Star', 'label' => '5-Star Reviews', 'description' => 'Customer satisfaction is our top priority']
                        ]
                    ],
                    'services' => [
                        'title' => 'Explore Our Premium Services',
                        'description' => 'Discover the wide range of services that can be booked using our powerful addon solution',
                        'button_text' => 'View All Services',
                        'button_url' => $user?->slug ? route('booking.services', ['userSlug' => $user->slug]) : null,
                        'card_popular_badge' => 'Most Popular',
                        'card_new_badge' => 'New',
                        'card_explore_text' => 'Explore Services'
                    ]
                ],
                'about' => [
                    'header' => [
                        'title' => 'About Us',
                        'description' => 'Learn more about our story, mission, and the team behind our success'
                    ],
                    'story' => [
                        'title' => 'Revolutionizing Bookings',
                        'image' => 'packages/workdo/Bookings/src/assets/images/about-img.png',
                        'content' => [
                            ['content' => 'Founded with a vision to simplify service bookings, we have transformed how people connect with professionals. Our platform bridges the gap between service seekers and providers, creating seamless experiences for everyone involved.']
                        ],
                        'stats' => [
                            ['number' => '10k+', 'label' => 'Happy Customers'],
                            ['number' => '500+', 'label' => 'Service Providers'],
                            ['number' => '50k+', 'label' => 'Bookings Completed'],
                            ['number' => '99%', 'label' => 'Satisfaction Rate']
                        ]
                    ],
                    'mission' => [
                        'title' => 'Our mission',
                        'subtitle' => '',
                        'content_title' => 'Revolutionizing Service Connections',
                        'content_description' => 'To create seamless connections between service seekers and providers, making quality services accessible to everyone while empowering professionals to grow their businesses.',
                        'features' => [
                            ['icon' => 'Users', 'title' => 'Community First', 'description' => 'Building stronger communities through trusted service connections'],
                            ['icon' => 'Shield', 'title' => 'Trust & Safety', 'description' => 'Ensuring secure transactions and verified service providers'],
                            ['icon' => 'Zap', 'title' => 'Innovation', 'description' => 'Continuously improving our platform with cutting-edge technology']
                        ]
                    ],
                    'team' => [
                        'title' => 'Meet Our Team',
                        'subtitle' => '',
                        'members' => [
                            ['image' => 'packages/workdo/Bookings/src/assets/images/team-1.png', 'name' => 'Sarah Martinez', 'position' => 'CEO & Founder', 'description' => 'Visionary leader with 10+ years in tech, passionate about connecting people with quality services.'],
                            ['image' => 'packages/workdo/Bookings/src/assets/images/team-2.png', 'name' => 'John Anderson', 'position' => 'CTO', 'description' => 'Technology expert driving innovation with cutting-edge solutions and scalable architecture.'],
                            ['image' => 'packages/workdo/Bookings/src/assets/images/team-3.png', 'name' => 'Emily Chen', 'position' => 'Head of Operations', 'description' => 'Operations specialist ensuring seamless service delivery and customer satisfaction.']
                        ]
                    ]
                ],
                'services' => [
                    'header' => [
                        'title' => 'Our Services',
                        'description' => 'Discover our comprehensive range of professional services designed to meet your needs'
                    ],
                    'search' => [
                        'search_placeholder' => 'Search for services...',
                        'search_button_text' => 'Search'
                    ],
                    'empty_state' => [
                        'title' => 'No services found',
                        'description' => 'Try adjusting your search terms or browse all services.'
                    ]
                ],
                'contact' => [
                    'header' => [
                        'title' => 'Contact Us',
                        'description' => 'Get in touch with us for any questions, support, or booking assistance'
                    ],
                    'form' => [
                        'form_title' => 'Send Us a Message',
                        'form_description' => 'Fill out the form below and our team will get back to you within 24 hours.',
                        'button_text' => 'Send Message'
                    ],
                    'info' => [
                        'title' => 'Connect With Us',
                        'address' => '123 Booking Street, Suite 101, New York, NY 10001',
                        'phone' => '+1 (555) 123-4567',
                        'email' => 'info@bookingpro.com',
                        'hours' => 'Mon-Fri: 9:00 AM - 6:00 PM',
                        'social_links' => [
                            ['platform' => 'facebook', 'icon' => 'Facebook', 'url' => 'https://facebook.com/bookingpro'],
                            ['platform' => 'twitter', 'icon' => 'Twitter', 'url' => 'https://twitter.com/bookingpro'],
                            ['platform' => 'instagram', 'icon' => 'Instagram', 'url' => 'https://instagram.com/bookingpro']
                        ]
                    ],
                    'map' => [
                        'embed_code' => '<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d29752.294817372065!2d72.87603200000001!3d21.2303872!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sin!4v1758705752443!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
                        'height' => '320'
                    ]
                ],
                'service_detail' => [
                    'header' => [
                        'title' => 'Service Details',
                        'description' => 'Complete information about our professional services'
                    ],
                    'reviews' => [
                        'section_title' => 'Customer Reviews',
                        'button_text' => 'Write Review',
                        'modal_title' => 'Write a Review',
                        'submit_button' => 'Submit Review',
                        'empty_message' => 'No reviews yet. Be the first to write a review!',
                        'form_placeholders' => [
                            'name' => 'Enter your name',
                            'email' => 'Enter your email',
                            'comment' => 'Tell us about your experience...'
                        ]
                    ]
                ],
                'notfound' => [
                    'notfound' => [
                        'title' => 'Page Not Found',
                        'heading' => 'Oops! Page Not Found',
                        'description' => 'The page you\'re looking for seems to have wandered off. Let\'s get you back on track!',
                        'home_button_text' => 'Back to Home',
                        'back_button_text' => 'Go Back',
                        'navigation_title' => 'Quick Navigation',
                        'navigation_items' => [
                            ['icon' => 'fas fa-concierge-bell', 'title' => 'Our Services', 'description' => 'Explore our premium services', 'url' => 'booking.services'],
                            ['icon' => 'fas fa-users', 'title' => 'About Us', 'description' => 'Learn more about our story', 'url' => 'booking.about'],
                            ['icon' => 'fas fa-envelope', 'title' => 'Contact Us', 'description' => 'Get in touch with our team', 'url' => 'booking.contact']
                        ]
                    ]
                ]
            ]
        ];
    }
    
    public function getConfigDataAttribute($value)
    {
        $decoded = json_decode($value, true) ?? [];
        return $this->mergeConfig(self::getDefaultConfig(), $decoded);
    }
    
    private function mergeConfig($default, $custom)
    {
        foreach ($custom as $key => $value) {
            if (is_array($value) && !isset($default[$key]) && is_array($default[$key])) {
                $default[$key] = $this->mergeConfig($default[$key], $value);
            } else {
                $default[$key] = $value;
            }
        }
        return $default;
    }
    
    public static function getSettings($userId = null)
    {
        $userId = $userId ?: auth()->id() ?: 1;
        $settings = self::where('created_by', $userId)->first();
        if (!$settings) {
            $settings = self::create([
                'config_data' => self::getDefaultConfig(),
                'created_by' => $userId
            ]);
        }
        return $settings;
    }
    
    public static function defaultdata($userId)
    {
        $settings = self::where('created_by', $userId)->first();
        if (!$settings) {
            $settings = self::create([
                'config_data' => self::getDefaultConfig(),
                'created_by' => $userId
            ]);
        }
        return $settings;
    }
}