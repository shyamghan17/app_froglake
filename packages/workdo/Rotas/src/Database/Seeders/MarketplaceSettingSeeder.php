<?php

namespace Workdo\Rotas\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/Rotas/src/marketplace/' . $file->getFilename();
                }
            }
        }
        
        sort($screenshots);
        
        MarketplaceSetting::firstOrCreate(['module' => 'Rotas'], [
            'module' => 'Rotas',
            'title' => 'Rotas Management Add-On',
            'subtitle' => 'Comprehensive workforce scheduling with visual dashboard and real-time updates',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Rotas Management Add-On for WorkDo Dash',
                        'subtitle' => 'The Rotas Management Add-On simplifies workforce scheduling with a visual dashboard, customizable schedules, and real-time rota updates. It supports multi-branch planning, employee availability tracking, efficient shift management, leave applications, and employee announcements across departments and locations.',
                        'primary_button_text' => 'Install Rotas Add-On',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/Rotas/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Rotas Add-on Features',
                        'subtitle' => 'Comprehensive workforce scheduling solution'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Complete Workforce Management Solution',
                        'description' => 'Streamline your employee scheduling with powerful visual tools and automated workflows.',
                        'subSections' => [
                            [
                                'title' => 'Visual Schedule Dashboard',
                                'description' => 'Simple visual dashboard with colour-coded calendar showing working shifts (blue), day-offs (orange), and leave days (red). Track total employees, published rotas, and pending assignments with monthly, weekly, and daily views.',
                                'keyPoints' => [
                                    'Colour-coded calendar for easy visualization',
                                    'Track total employees and pending rotas',
                                    'Multiple calendar view options',
                                    'Real-time schedule updates'
                                ],
                                'screenshot' => '/packages/workdo/Rotas/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Smart Availability & Shift Management',
                                'description' => 'Track employee availability with start and end dates, manage work shifts with break periods, and prevent scheduling conflicts. Configure paid/unpaid breaks and night shift marking.',
                                'keyPoints' => [
                                    'Employee availability tracking',
                                    'Shift management with break configuration',
                                    'Conflict prevention system',
                                    'Night shift support'
                                ],
                                'screenshot' => '/packages/workdo/Rotas/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Leave Management & Communication',
                                'description' => 'Complete leave application system with approval workflows, automatic balance calculations, and department-specific announcements with priority levels and date ranges.',
                                'keyPoints' => [
                                    'Leave application and approval workflow',
                                    'Automatic leave balance tracking',
                                    'Department-specific announcements',
                                    'Priority-based communication'
                                ],
                                'screenshot' => '/packages/workdo/Rotas/src/marketplace/image3.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Rotas Add-on in Action',
                        'subtitle' => 'See how our workforce scheduling tools improve your operations',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Rotas Add-on?',
                        'subtitle' => 'Streamline workforce scheduling with comprehensive management tools',
                        'benefits' => [
                            [
                                'title' => 'Visual Schedule Dashboard',
                                'description' => 'Colour-coded calendar with multiple view options for easy workforce planning.',
                                'icon' => 'Calendar',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Multi-Branch Planning',
                                'description' => 'Manage schedules across multiple locations with department-wise organization.',
                                'icon' => 'Building',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Employee Availability',
                                'description' => 'Track when employees can work and prevent scheduling conflicts.',
                                'icon' => 'Users',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Leave Management',
                                'description' => 'Complete leave application system with automatic balance tracking.',
                                'icon' => 'FileText',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Real-Time Updates',
                                'description' => 'Publish rotas in real-time with instant notifications to employees.',
                                'icon' => 'Zap',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Employee Communication',
                                'description' => 'Department-specific announcements with priority levels and scheduling.',
                                'icon' => 'MessageSquare',
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