<?php

namespace Workdo\BulkSMS\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/BulkSMS/src/marketplace/' . $file->getFilename();
                }
            }
        }
        
        sort($screenshots);
        
        MarketplaceSetting::firstOrCreate(['module' => 'BulkSMS'], [
            'module' => 'BulkSMS',
            'title' => 'Bulk SMS Module Marketplace',
            'subtitle' => 'Comprehensive bulk SMS messaging tools for your applications',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Bulk SMS Module for WorkDo Dash',
                        'subtitle' => 'Streamline your SMS communication workflow with comprehensive messaging tools and automated campaigns.',
                        'primary_button_text' => 'Install Bulk SMS Module',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/BulkSMS/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Bulk SMS Module',
                        'subtitle' => 'Enhance your communication with powerful bulk SMS messaging tools'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Dedicated Bulk SMS Features',
                        'description' => 'Our bulk SMS module provides comprehensive messaging capabilities for modern communication needs.',
                        'subSections' => [
                            [
                                'title' => 'Mass SMS Campaign Management',
                                'description' => 'Create and manage large-scale SMS campaigns with contact list management, message templates, and scheduled delivery. Send personalized messages to thousands of recipients with automated campaign tracking and delivery reports.',
                                'keyPoints' => ['Contact list management', 'Message template system', 'Scheduled SMS delivery', 'Campaign tracking reports'],
                                'screenshot' => '/packages/workdo/BulkSMS/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'SMS Gateway Integration',
                                'description' => 'Connect with multiple SMS service providers for reliable message delivery with automatic failover support. Manage different SMS gateways, configure priority routing, and ensure maximum message delivery success rates.',
                                'keyPoints' => ['Multiple SMS provider support', 'Automatic failover system', 'Priority routing configuration', 'High delivery success rates'],
                                'screenshot' => '/packages/workdo/BulkSMS/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'SMS Delivery and Status Tracking',
                                'description' => 'Monitor SMS delivery status with real-time tracking, delivery confirmations, and failed message management. Track message status from sent to delivered with comprehensive logging and retry mechanisms for failed deliveries.',
                                'keyPoints' => ['Real-time delivery tracking', 'Message status monitoring', 'Failed delivery management', 'Comprehensive delivery logs'],
                                'screenshot' => '/packages/workdo/BulkSMS/src/marketplace/image3.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Bulk SMS Module in Action',
                        'subtitle' => 'See how our SMS messaging tools improve your communication workflow',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Bulk SMS Module?',
                        'subtitle' => 'Improve communication efficiency with comprehensive SMS messaging',
                        'benefits' => [
                            [
                                'title' => 'Mass Communication',
                                'description' => 'Send thousands of SMS messages instantly with automated campaign management and delivery tracking.',
                                'icon' => 'MessageSquare',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'SMS Gateway Management',
                                'description' => 'Manage multiple SMS service providers with intelligent routing and automatic failover for reliable delivery.',
                                'icon' => 'Zap',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Delivery Tracking',
                                'description' => 'Track SMS delivery status in real-time with comprehensive logging and failed message management.',
                                'icon' => 'CheckCircle',
                                'color' => 'purple'
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