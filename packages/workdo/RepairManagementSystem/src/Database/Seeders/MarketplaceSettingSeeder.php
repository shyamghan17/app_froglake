<?php

namespace Workdo\RepairManagementSystem\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/RepairManagementSystem/src/marketplace/' . $file->getFilename();
                }
            }
        }

        sort($screenshots);

        MarketplaceSetting::firstOrCreate(['module' => 'RepairManagementSystem'], [
            'module' => 'RepairManagementSystem',
            'title' => 'Repair Management System Module Marketplace',
            'subtitle' => 'Comprehensive repair management system tools for your applications',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Repair Management System Module for WorkDo Dash',
                        'subtitle' => 'Streamline your repair management system workflow with comprehensive tools and automated management.',
                        'primary_button_text' => 'Install Repair Management System Module',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/RepairManagementSystem/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Repair Management System Module',
                        'subtitle' => 'Enhance your workflow with powerful repair management system tools'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Dedicated Repair Management System Features',
                        'description' => 'Our repair management system module provides comprehensive capabilities for modern workflows.',
                        'subSections' => [
                            [
                                'title' => 'Smart Repair Order Management',
                                'description' => 'Streamline your repair workflow with intelligent order processing that automatically assigns technicians, tracks progress, and manages customer communications. Our comprehensive system handles everything from initial repair requests to final delivery, ensuring efficient operations and enhanced customer satisfaction.',
                                'keyPoints' => ['Automated order processing', 'Technician assignment system', 'Real-time progress tracking', 'Customer communication portal'],
                                'screenshot' => '/packages/workdo/RepairManagementSystem/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Advanced Technician Management',
                                'description' => 'Optimize your repair team efficiency with comprehensive technician management that tracks skills, availability, workload, and performance metrics. Our intelligent system ensures optimal task assignment based on expertise and capacity while maintaining detailed records of all repair activities.',
                                'keyPoints' => ['Skill-based task assignment', 'Workload balancing system', 'Performance analytics dashboard', 'Availability scheduling tools'],
                                'screenshot' => '/packages/workdo/RepairManagementSystem/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Comprehensive Parts & Inventory Control',
                                'description' => 'Maintain optimal inventory levels with intelligent parts management that tracks usage patterns, automates reordering, and manages supplier relationships. Our advanced system provides real-time inventory visibility, cost tracking, and predictive analytics to minimize downtime and reduce carrying costs.',
                                'keyPoints' => ['Real-time inventory tracking', 'Automated reorder management', 'Supplier relationship tools', 'Cost analysis and reporting'],
                                'screenshot' => '/packages/workdo/RepairManagementSystem/src/marketplace/image3.png'
                            ],
                            [
                                'title' => 'Integrated Warranty & Financial Management',
                                'description' => 'Streamline your financial operations with automated invoicing, warranty tracking, and payment processing that ensures accurate billing and compliance. Our comprehensive system manages warranty claims, generates detailed financial reports, and provides insights into repair profitability and customer payment patterns.',
                                'keyPoints' => ['Automated invoice generation', 'Warranty claim processing', 'Payment tracking system', 'Financial reporting dashboard'],
                                'screenshot' => '/packages/workdo/RepairManagementSystem/src/marketplace/image4.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Repair Management System Module in Action',
                        'subtitle' => 'See how our repair management system tools improve your workflow',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Repair Management System Module?',
                        'subtitle' => 'Improve efficiency with comprehensive repair management system management',
                        'benefits' => [
                            [
                                'title' => 'Smart Order Processing',
                                'description' => 'Automated repair order management with intelligent technician assignment and real-time progress tracking.',
                                'icon' => 'ClipboardList',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Technician Optimization',
                                'description' => 'Skill-based task assignment with workload balancing and performance analytics for maximum efficiency.',
                                'icon' => 'Users',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Inventory Control',
                                'description' => 'Real-time parts tracking with automated reordering and supplier management to minimize downtime.',
                                'icon' => 'Package',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Warranty Management',
                                'description' => 'Automated warranty claim processing with comprehensive tracking and compliance management.',
                                'icon' => 'Shield',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Financial Integration',
                                'description' => 'Automated invoicing, payment tracking, and financial reporting for complete repair profitability insights.',
                                'icon' => 'DollarSign',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Customer Portal',
                                'description' => 'Enhanced customer communication with real-time repair status updates and automated notifications.',
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
