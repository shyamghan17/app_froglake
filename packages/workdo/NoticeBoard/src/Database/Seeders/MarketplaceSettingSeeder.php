<?php

namespace Workdo\NoticeBoard\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\LandingPage\Models\MarketplaceSetting;
use Illuminate\Support\Facades\File;

class MarketplaceSettingSeeder extends Seeder
{
    public function run()
    {
        $marketplaceDir = __DIR__ . '/../../marketplace';
        $screenshots    = [];

        if (File::exists($marketplaceDir)) {
            $files = File::files($marketplaceDir);
            foreach ($files as $file) {
                if (in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
                    $screenshots[] = '/packages/workdo/NoticeBoard/src/marketplace/' . $file->getFilename();
                }
            }
        }

        sort($screenshots);

        MarketplaceSetting::firstOrCreate(['module' => 'NoticeBoard'], [
            'module'          => 'NoticeBoard',
            'title'           => 'NoticeBoard Module Marketplace',
            'subtitle'        => 'Comprehensive internal notice management and communication tools for your organisation',
            'config_sections' => [
                'sections'           => [
                    'hero'        => [
                        'variant'               => 'hero1',
                        'title'                 => 'NoticeBoard Module for WorkDo Dash',
                        'subtitle'              => 'Streamline internal communication with structured notice publishing, targeted delivery, acknowledgment tracking and real-time critical alerts.',
                        'primary_button_text'   => 'Install NoticeBoard Module',
                        'primary_button_link'   => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image'                 => '/packages/workdo/NoticeBoard/src/marketplace/hero.png',
                    ],
                    'modules'     => [
                        'variant'  => 'modules1',
                        'title'    => 'NoticeBoard Module',
                        'subtitle' => 'Enhance your internal communication with powerful notice management and real-time alert tools',
                    ],
                    'dedication'  => [
                        'variant'     => 'dedication1',
                        'title'       => 'Dedicated NoticeBoard Features',
                        'description' => 'Our NoticeBoard module provides comprehensive internal communication capabilities for modern organisations.',
                        'subSections' => [
                            [
                                'title'       => 'Notice Board & Notice Management',
                                'description' => 'Create and manage notices with rich text editor, file attachments, priority levels and full lifecycle management from draft through to deactivation. Schedule notices with start and expiry dates, pin important notices to the top and manage publish and deactivate workflows with permission-based controls.',
                                'keyPoints'   => [
                                    'Normal, Urgent and Critical priority levels with colour-coded badges',
                                    'Draft, Published and Deactivated notice lifecycle management',
                                    'Start date and expiry date scheduling with automatic visibility',
                                    'List and grid view with search, filters and per-page pagination',
                                ],
                                'screenshot'  => '/packages/workdo/NoticeBoard/src/marketplace/image1.png',
                            ],
                            [
                                'title'       => 'Targeted Delivery & Audience Control',
                                'description' => 'Control who receives each notice with four targeting modes including all company users, specific departments, roles or individually selected users. Company-scoped visibility ensures notices reach only the right people within your organisation.',
                                'keyPoints'   => [
                                    'Target all users, departments, roles or specific users',
                                    'HRM module integration for department-based targeting',
                                    'Searchable multi-select for role and user targeting',
                                    'Company-scoped visibility with admin and staff permission levels',
                                ],
                                'screenshot'  => '/packages/workdo/NoticeBoard/src/marketplace/image2.png',
                            ],
                            [
                                'title'       => 'Read Tracking, Acknowledgment & Statistics',
                                'description' => 'Monitor notice engagement with automatic read tracking, formal acknowledgment requirements and detailed per-notice statistics. View who has read, who has acknowledged and who is yet to read each notice with individual timestamps and engagement rate indicators.',
                                'keyPoints'   => [
                                    'Automatic read tracking when user opens the notice detail page',
                                    'Manual mark-as-read from the board without opening full notice',
                                    'Formal acknowledgment requirement on critical notices',
                                    'Read rate and acknowledgment rate with unread user list',
                                ],
                                'screenshot'  => '/packages/workdo/NoticeBoard/src/marketplace/image3.png',
                            ],
                            [
                                'title'       => 'Real-Time Critical Alerts',
                                'description' => 'Deliver instant popup alerts to targeted employees the moment a critical notice is published, ensuring urgent communications are never missed. Users can acknowledge or dismiss alerts directly from the popup and any missed alerts are automatically shown on their next login.',
                                'keyPoints'   => [
                                    'Instant real-time popup alert on critical notice publish',
                                    'Blocking popup with full notice content and attachment previews',
                                    'Acknowledge or dismiss directly from the alert popup',
                                    'Missed alerts automatically shown on next login',
                                ],
                                'screenshot'  => '/packages/workdo/NoticeBoard/src/marketplace/image4.png',
                            ],
                        ],
                    ],
                    'screenshots' => [
                        'variant'  => 'screenshots1',
                        'title'    => 'NoticeBoard Module in Action',
                        'subtitle' => 'See how our notice management tools improve internal communication',
                        'images'   => $screenshots,
                    ],
                    'why_choose'  => [
                        'variant'  => 'whychoose1',
                        'title'    => 'Why Choose NoticeBoard Module?',
                        'subtitle' => 'Improve internal communication with structured notice management and real-time alerts',
                        'benefits' => [
                            [
                                'title'       => 'Priority-Based Notices',
                                'description' => 'Classify notices as Normal, Urgent or Critical so employees always know what needs immediate attention.',
                                'icon'        => 'AlertTriangle',
                                'color'       => 'red',
                            ],
                            [
                                'title'       => 'Targeted Delivery',
                                'description' => 'Send notices to all staff, specific departments, roles or individual users for precise communication.',
                                'icon'        => 'Target',
                                'color'       => 'blue',
                            ],
                            [
                                'title'       => 'Acknowledgment Tracking',
                                'description' => 'Require formal acknowledgment on critical notices and monitor compliance with per-user timestamps.',
                                'icon'        => 'CheckCircle',
                                'color'       => 'green',
                            ],
                            [
                                'title'       => 'Read Statistics',
                                'description' => 'View detailed read and unread stats per notice with individual user status and engagement rates.',
                                'icon'        => 'BarChart2',
                                'color'       => 'purple',
                            ],
                            [
                                'title'       => 'Threaded Comments',
                                'description' => 'Enable contextual discussions on notices with threaded replies and permission-based visibility.',
                                'icon'        => 'MessageSquare',
                                'color'       => 'yellow',
                            ],
                            [
                                'title'       => 'Real-Time Critical Alerts',
                                'description' => 'Push instant blocking alerts for critical notices so no important update is ever missed.',
                                'icon'        => 'Bell',
                                'color'       => 'indigo',
                            ],
                        ],
                    ],
                ],
                'section_visibility' => [
                    'header'      => true,
                    'hero'        => true,
                    'modules'     => true,
                    'dedication'  => true,
                    'screenshots' => true,
                    'why_choose'  => true,
                    'cta'         => true,
                    'footer'      => true,
                ],
                'section_order' => ['header', 'hero', 'modules', 'dedication', 'screenshots', 'why_choose', 'cta', 'footer'],
            ],
        ]);
    }
}
