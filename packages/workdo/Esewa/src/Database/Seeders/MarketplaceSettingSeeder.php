<?php

namespace Workdo\Esewa\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/Esewa/src/marketplace/' . $file->getFilename();
                }
            }
        }
        
        sort($screenshots);
        
        MarketplaceSetting::firstOrCreate(['module' => 'Esewa'], [
            'module' => 'Esewa',
            'title' => 'eSewa Payment Gateway',
            'subtitle' => 'Nepal\'s leading digital payment solution for secure online transactions',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'eSewa Payment Gateway Integration',
                        'subtitle' => 'Accept payments across Nepal with the country\'s most popular digital wallet platform. Enable secure NPR transactions with multiple payment methods and expand your business reach.',
                        'primary_button_text' => 'Install eSewa Gateway',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/Esewa/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'eSewa Payment Gateway',
                        'subtitle' => 'Supported Country: Nepal. Supported Currency: NPR (Nepalese Rupee)'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Comprehensive Payment Solutions',
                        'description' => 'Our eSewa integration provides robust payment processing specifically designed for Nepalese markets with advanced security and seamless user experience.',
                        'subSections' => [
                            [
                                'title' => 'Merchant Configuration Dashboard',
                                'description' => 'The Merchant Configuration Dashboard enables secure setup of your eSewa payment gateway with comprehensive settings management. Configure merchant credentials, payment modes, and currency settings to establish reliable payment processing for your platform.',
                                'keyPoints' => ['Secure Merchant ID Setup', 'Payment Mode Configuration', 'NPR Currency Integration', 'Real-time Settings Validation', 'Sandbox & Live Environment Support', 'Advanced Security Configuration'],
                                'screenshot' => '/packages/workdo/Esewa/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Multi-Module Payment Processing',
                                'description' => 'eSewa Payment Gateway supports comprehensive payment processing across all platform modules including subscriptions, bookings, and services. Handle plan payments, beauty spa bookings, LMS courses, hotel reservations, and event tickets with unified payment experience.',
                                'keyPoints' => ['Plan & Subscription Payments', 'Booking & Appointment Processing', 'LMS Course Purchases', 'Hotel & Room Reservations', 'Event Ticket Sales', 'Service-based Transactions'],
                                'screenshot' => '/packages/workdo/Esewa/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Advanced Payment Management',
                                'description' => 'Manage all eSewa transactions through centralized dashboard with real-time monitoring and automated reconciliation features. Track payment status, handle refunds, generate financial reports, and maintain complete audit trails for all NPR transactions.',
                                'keyPoints' => ['Centralized Transaction Dashboard', 'Real-time Payment Monitoring', 'Automated Status Updates', 'Financial Reporting & Analytics', 'Refund Management System', 'Complete Audit Trail Tracking'],
                                'screenshot' => '/packages/workdo/Esewa/src/marketplace/image3.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'eSewa Gateway in Action',
                        'subtitle' => 'Experience seamless Nepalese payment processing and management',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose eSewa Gateway?',
                        'subtitle' => 'Expand across Nepal with trusted digital payment infrastructure',
                        'benefits' => [
                            [
                                'title' => 'Instant Digital Payments',
                                'description' => 'Process digital wallet payments instantly across Nepal with 99.9% uptime and local NPR currency support.',
                                'icon' => 'Play',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Comprehensive Analytics',
                                'description' => 'Access detailed Nepalese market analytics and generate custom financial reports for better insights.',
                                'icon' => 'FileText',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Multi-User Access Control',
                                'description' => 'Manage team access with role-based permissions and collaborative payment management features.',
                                'icon' => 'Users',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Rapid Integration Setup',
                                'description' => 'Deploy eSewa gateway in minutes with simple configuration and comprehensive documentation support.',
                                'icon' => 'GitBranch',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Enterprise-Grade Security',
                                'description' => 'Ensure secure transactions with advanced encryption and fraud protection mechanisms.',
                                'icon' => 'CheckCircle',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Business Growth Analytics',
                                'description' => 'Monitor Nepalese market performance and optimize payment success rates for maximum revenue.',
                                'icon' => 'Activity',
                                'color' => 'indigo'
                            ],
                            [
                                'title' => 'Mobile-First Experience',
                                'description' => 'Optimized for mobile payments with responsive design and seamless user experience.',
                                'icon' => 'Smartphone',
                                'color' => 'pink'
                            ],
                            [
                                'title' => '24/7 Transaction Support',
                                'description' => 'Round-the-clock payment processing with automated error handling and recovery systems.',
                                'icon' => 'Clock',
                                'color' => 'orange'
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