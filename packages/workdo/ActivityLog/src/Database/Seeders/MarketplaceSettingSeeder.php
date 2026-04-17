<?php

namespace Workdo\ActivityLog\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/ActivityLog/src/marketplace/' . $file->getFilename();
                }
            }
        }

        sort($screenshots);

        MarketplaceSetting::firstOrCreate(['module' => 'ActivityLog'], [
            'module' => 'ActivityLog',
            'title' => 'Activity Log Module Marketplace',
            'subtitle' => 'Comprehensive activity log tools for your applications',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Activity Log Module for WorkDo Dash',
                        'subtitle' => 'Streamline your activity log workflow with comprehensive tools and automated management.',
                        'primary_button_text' => 'Install Activity Log Module',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/ActivityLog/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Activity Log Module',
                        'subtitle' => 'Enhance your workflow with powerful activity log tools'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Dedicated Activity Log Features',
                        'description' => 'Our activity log module provides comprehensive capabilities for modern workflows.',
                        'subSections' => [
                            [
                                'title' => 'Real-Time Activity Tracking',
                                'description' => 'Monitor all user actions and system changes in real-time with detailed timestamps and user information. Capture every create, update, and delete operation across all modules for complete transparency.',
                                'keyPoints' => ['Live activity monitoring dashboard', 'Detailed user action logging', 'Timestamp and IP tracking', 'Cross-module activity capture'],
                                'screenshot' => '/packages/workdo/ActivityLog/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Advanced Filtering & Analytics',
                                'description' => 'Filter and analyze activities by user, date, module, or action type with powerful search capabilities. Generate detailed reports and insights for business intelligence and security monitoring.',
                                'keyPoints' => ['Advanced search and filtering', 'Activity analytics dashboard', 'Custom report generation', 'Security monitoring alerts'],
                                'screenshot' => '/packages/workdo/ActivityLog/src/marketplace/image2.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Activity Log Module in Action',
                        'subtitle' => 'See how our activity log tools improve your workflow',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Activity Log Module?',
                        'subtitle' => 'Improve efficiency with comprehensive activity log management',
                        'benefits' => [
                            [
                                'title' => 'Automated Process',
                                'description' => 'Automate your activity log workflow to save time and reduce errors.',
                                'icon' => 'Play',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Comprehensive Reports',
                                'description' => 'Get detailed reports with metrics and performance data.',
                                'icon' => 'FileText',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Team Collaboration',
                                'description' => 'Share results and collaborate effectively with your team.',
                                'icon' => 'Users',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Easy Integration',
                                'description' => 'Seamlessly integrate with your existing workflow.',
                                'icon' => 'GitBranch',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Quality Management',
                                'description' => 'Maintain high quality with comprehensive management tools.',
                                'icon' => 'CheckCircle',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Performance Tracking',
                                'description' => 'Track performance and identify improvements early.',
                                'icon' => 'Activity',
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
