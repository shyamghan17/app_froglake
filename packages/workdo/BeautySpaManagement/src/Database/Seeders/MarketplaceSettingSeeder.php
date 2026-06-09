<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/BeautySpaManagement/src/marketplace/' . $file->getFilename();
                }
            }
        }
        
        sort($screenshots);
        
        MarketplaceSetting::firstOrCreate(['module' => 'BeautySpaManagement'], [
            'module' => 'BeautySpaManagement',
            'title' => 'Beauty Spa Management Module Marketplace',
            'subtitle' => 'Complete spa and beauty salon management solution for modern wellness businesses',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Beauty Spa Management Module for WorkDo Dash',
                        'subtitle' => 'Transform your spa and beauty salon operations with comprehensive booking management, service tracking, and customer loyalty programs for enhanced business growth.',
                        'primary_button_text' => 'Install Beauty Spa Management Module',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/BeautySpaManagement/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Beauty Spa Management Module',
                        'subtitle' => 'Enhance your spa operations with powerful booking and customer management tools'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Dedicated Beauty Spa Management Features',
                        'description' => 'Our beauty spa management module provides comprehensive capabilities for modern spa and salon operations with integrated booking and customer management systems.',
                        'subSections' => [
                            [
                                'title' => 'Comprehensive Booking & Appointment Management',
                                'description' => 'Streamline your spa appointments with advanced booking system that handles scheduling, customer preferences, and service assignments with real-time availability tracking. Online bookings, and recurring appointments while maintaining detailed customer profiles and service history for personalized experiences.',
                                'keyPoints' => ['Real-time appointment scheduling system', 'Customer preference tracking management', 'Service staff assignment automation', 'Booking confirmation notification system'],
                                'screenshot' => '/packages/workdo/BeautySpaManagement/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Service Management & Loyalty Programs',
                                'description' => 'Organize your spa services, packages, and membership programs with detailed pricing structures and promotional offers for enhanced customer retention. Track service performance, manage gift cards, and create customized treatment packages while implementing comprehensive loyalty programs to build lasting customer relationships.',
                                'keyPoints' => ['Service catalog pricing management', 'Membership program tracking system', 'Gift card management integration', 'Loyalty program reward system'],
                                'screenshot' => '/packages/workdo/BeautySpaManagement/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Exclusive Offers',
                                'description' => 'Attract more customers with eye-catching promotional offers that showcase your best deals and limited-time discounts. Create beautiful offer cards showing original prices crossed out with new discounted prices, percentage savings badges, and clear expiration dates. Each offer includes attractive descriptions, service benefits, and direct booking buttons that lead customers straight to appointment scheduling.',
                                'keyPoints' => ['Percentage discount badges and clear promotional campaign themes','Direct booking buttons linking offers to appointment scheduling system','Seasonal campaign organization with expiration dates and limited-time messaging'],
                                'screenshot' => '/packages/workdo/BeautySpaManagement/src/marketplace/image3.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Beauty Spa Management Module in Action',
                        'subtitle' => 'See how our spa management tools transform your beauty business operations',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Beauty Spa Management Module?',
                        'subtitle' => 'Improve efficiency with comprehensive spa and beauty salon management',
                        'benefits' => [
                            [
                                'title' => 'Smart Booking Dashboard',
                                'description' => 'Real-time appointment scheduling with automated staff assignments.',
                                'icon' => 'Calendar',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Loyalty Programs',
                                'description' => 'Reward system with gift cards and membership tracking.',
                                'icon' => 'Gift',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Payment Integration',
                                'description' => 'Seamless payment processing with receipt generation.',
                                'icon' => 'CreditCard',
                                'color' => 'green'
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