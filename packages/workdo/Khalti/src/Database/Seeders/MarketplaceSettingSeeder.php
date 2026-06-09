<?php

namespace Workdo\Khalti\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/Khalti/src/marketplace/' . $file->getFilename();
                }
            }
        }

        sort($screenshots);

        MarketplaceSetting::firstOrCreate(['module' => 'Khalti'], [
            'module' => 'Khalti',
            'title' => 'Khalti Payment Gateway',
            'subtitle' => 'Secure payment processing for Nepal with Khalti',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Khalti Payment Gateway for WorkDo Dash',
                        'subtitle' => 'Accept payments securely with Nepal\'s leading digital payment gateway. Enable seamless transactions for subscriptions, bookings, and e-commerce.',
                        'primary_button_text' => 'Install Khalti Module',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/Khalti/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Khalti Payment Gateway',
                        'subtitle' => 'Complete payment solution with advanced features and security'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Comprehensive Payment Features',
                        'description' => 'Our Khalti integration delivers secure payment processing with seamless user experience across all business modules.',
                        'subSections' => [
                            [
                                'title' => 'API Key Settings for Khalti Integration',
                                'description' => 'The API Key Settings Page allows you to securely connect your application with the Khalti payment gateway by entering merchant credentials. Both Superadmin and Company accounts can utilize this integration, with Superadmin managing subscription-based plan payments and Company-level handling invoices and one-time transactions.',
                                'keyPoints' => ['Secure Merchant Credential Setup', 'Superadmin Subscription Management', 'Company Invoice Payment Processing', 'Unified Integration Configuration'],
                                'screenshot' => '/packages/workdo/Khalti/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Seamless Plan Payments',
                                'description' => 'Khalti Payment allows you to manage recurring payments for subscription-based plans effortlessly with secure customer authorization. Enable smooth and consistent payment processing for ongoing services across Plan, Bookings, Beauty Spa, LMS, Hotel & Room Management modules.',
                                'keyPoints' => ['Recurring Subscription Payments', 'Secure Customer Authorization', 'Multi-Module Payment Support', 'Automated Plan Renewals'],
                                'screenshot' => '/packages/workdo/Khalti/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Comprehensive Payment Management',
                                'description' => 'Manage all Khalti payment processes through centralized dashboard with real-time transaction monitoring and automated reconciliation. Handle invoice payments, subscription renewals, and one-time transactions with complete payment lifecycle tracking and detailed financial reporting.',
                                'keyPoints' => ['Centralized Payment Dashboard', 'Real-time Transaction Monitoring', 'Automated Payment Reconciliation', 'Complete Lifecycle Tracking'],
                                'screenshot' => '/packages/workdo/Khalti/src/marketplace/image3.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Khalti Gateway in Action',
                        'subtitle' => 'Experience seamless payment processing and management',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Khalti Gateway?',
                        'subtitle' => 'Boost revenue with Nepal\'s most trusted payment solution',
                        'benefits' => [
                            [
                                'title' => 'Instant Payment Processing',
                                'description' => 'Process payments instantly with real-time transaction updates and confirmation.',
                                'icon' => 'Zap',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Detailed Transaction Reports',
                                'description' => 'Access comprehensive payment analytics and generate custom financial reports.',
                                'icon' => 'BarChart3',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Multi-User Dashboard',
                                'description' => 'Manage team access with role-based permissions and collaborative features.',
                                'icon' => 'Users',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Quick Setup Integration',
                                'description' => 'Deploy payment gateway in minutes with simple API integration and documentation.',
                                'icon' => 'Settings',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Enterprise Security',
                                'description' => 'Bank-grade security with advanced fraud protection and compliance standards.',
                                'icon' => 'Shield',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Revenue Optimization',
                                'description' => 'Maximize conversions with smart routing and payment success rate monitoring.',
                                'icon' => 'TrendingUp',
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
