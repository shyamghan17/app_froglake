<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\LandingPage\Models\MarketplaceSetting;
use Illuminate\Support\Facades\File;

class MarketplaceSettingSeeder extends Seeder
{
    public function run()
    {
        // Get all available screenshots from marketplace directory
        $marketplaceDir = __DIR__ . '/../../marketplace';
        $screenshots = [];
        
        if (File::exists($marketplaceDir)) {
            $files = File::files($marketplaceDir);
            foreach ($files as $file) {
                if (in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
                    $screenshots[] = '/packages/workdo/Bookings/src/marketplace/' . $file->getFilename();
                }
            }
        }
        
        sort($screenshots);
        
        MarketplaceSetting::firstOrCreate(['module' => 'Bookings'], [
            'module' => 'Bookings',
            'title' => 'Professional Service Booking System',
            'subtitle' => 'Complete appointment scheduling and service management platform',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Professional Service Booking System',
                        'subtitle' => 'Transform your service business with a complete booking platform featuring multi-language support, payment integration, and customer management.',
                        'primary_button_text' => 'Install Booking System',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'View Demo',
                        'secondary_button_link' => '#demo',
                        'image' => ''
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Service Booking Platform',
                        'subtitle' => 'Everything you need to manage appointments and grow your service business'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Comprehensive Booking Features',
                        'description' => 'Our booking system provides enterprise-grade capabilities for service businesses of all sizes.',
                        'subSections' => [
                            [
                                'title' => 'Service & Package Management',
                                'description' => 'Create and manage your service offerings with flexible pricing, duration settings, and package configurations. Organize services into categories with detailed descriptions, images, and customizable options to maximize revenue and customer satisfaction.',
                                'keyPoints' => [
                                    'Flexible service duration and pricing configuration',
                                    'Service package bundling with discount options',
                                    'Extra services and add-on management system',
                                    'Category organization for easy service discovery'
                                ],
                                'screenshot' => '/packages/workdo/Bookings/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Customer & Appointment Management',
                                'description' => 'Maintain comprehensive customer profiles and manage all appointments from a centralized system with detailed booking history and preferences. Track customer interactions, manage contact information, and build lasting relationships through personalized service delivery.',
                                'keyPoints' => [
                                    'Complete customer profiles with booking history',
                                    'Appointment status tracking and management',
                                    'Customer communication and contact management',
                                    'Booking preferences and service history tracking'
                                ],
                                'screenshot' => '/packages/workdo/Bookings/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Easy Appointment Control',
                                'description' => 'Simplify appointment scheduling with intuitive calendar and kanban views that make managing bookings effortless. Drag-and-drop functionality, quick status updates, and real-time availability checking ensure smooth operations and reduced scheduling conflicts.',
                                'keyPoints' => [
                                    'Calendar and Kanban views for appointment visualization',
                                    'Drag-and-drop appointment rescheduling functionality',
                                    'Real-time availability checking and conflict prevention',
                                    'Quick appointment status updates and modifications'
                                ],
                                'screenshot' => '/packages/workdo/Bookings/src/marketplace/image3.png'
                            ],
                            [
                                'title' => 'Staff Management & Workflow',
                                'description' => 'Efficiently manage your team with comprehensive staff profiles, availability scheduling, and workload distribution tools. Set individual staff permissions, track performance metrics, and optimize workflow to ensure maximum productivity and service quality.',
                                'keyPoints' => [
                                    'Staff availability management with scheduling controls',
                                    'Individual staff profiles and permission settings',
                                    'Workload distribution and performance tracking',
                                    'Staff-specific service assignments and specializations'
                                ],
                                'screenshot' => '/packages/workdo/Bookings/src/marketplace/image4.png'
                            ],
                            [
                                'title' => 'Customer Booking Experience',
                                'description' => 'Deliver an exceptional booking experience with an intuitive frontend portal that supports multiple languages and real-time availability. Your customers can easily browse services, select time slots, and complete bookings with a seamless, mobile-responsive interface.',
                                'keyPoints' => [
                                    'Multi-language booking forms with complete i18n support',
                                    'Real-time availability checking with instant slot updates',
                                    'Mobile-first responsive design for all screen sizes',
                                    'Custom branding with logo, colors, and theme options'
                                ],
                                'screenshot' => '/packages/workdo/Bookings/src/marketplace/image5.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Booking System in Action',
                        'subtitle' => 'See how our platform streamlines your service business operations',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Our Booking System?',
                        'subtitle' => 'Built for modern service businesses with security and scalability in mind',
                        'benefits' => [
                            [
                                'title' => 'Multi-Language Support',
                                'description' => 'Serve global customers with built-in internationalization and language switching.',
                                'icon' => 'Globe',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Secure & Compliant',
                                'description' => 'Enterprise-grade security with CSRF protection and input validation.',
                                'icon' => 'Shield',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Payment Integration',
                                'description' => 'Accept payments online with multiple gateway support and secure processing.',
                                'icon' => 'CreditCard',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Real-Time Availability',
                                'description' => 'Dynamic time slot management with conflict prevention and availability checking.',
                                'icon' => 'Clock',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Customer Management',
                                'description' => 'Complete CRM with customer profiles, booking history, and communication tools.',
                                'icon' => 'Users',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Analytics & Reports',
                                'description' => 'Comprehensive business intelligence with revenue tracking and performance metrics.',
                                'icon' => 'BarChart',
                                'color' => 'indigo'
                            ]
                        ]
                    ]
                ],
                'section_visibility' => [
                    'header' => true,
                    'hero' => true,
                    'modules' => true,
                    'dedication' => true,
                    'screenshots' => true,
                    'why_choose' => true,
                    'cta' => true,
                    'footer' => true
                ],
                'section_order' => ['header', 'hero', 'modules', 'dedication', 'screenshots', 'why_choose', 'cta', 'footer']
            ]
        ]);
    }
}