<?php

namespace Workdo\OpticalAndEyeCareCenter\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/OpticalAndEyeCareCenter/src/marketplace/' . $file->getFilename();
                }
            }
        }
        
        sort($screenshots);
        
        MarketplaceSetting::firstOrCreate(['module' => 'OpticalAndEyeCareCenter'], [
            'module' => 'OpticalAndEyeCareCenter',
            'title' => 'Optical & Eyecare Management Add-On',
            'subtitle' => 'Centralize all clinic and optical store operations in one streamlined platform',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Optical & Eyecare Management Add-On',
                        'subtitle' => 'Centralize all clinic and optical store operations, from patient and doctor management to prescriptions, appointments, and eyewear orders, in one streamlined platform.',
                        'primary_button_text' => 'Install Module',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/OpticalAndEyeCareCenter/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Powerful Tools for Clinics and Optical Stores',
                        'subtitle' => 'Centralized Eye Care Dashboard • Doctor & Patient Records • Eye Test Prescriptions • Appointment Scheduling • Eyewear Inventory Control • Order & Payment Tracking • Multi-Filter Smart Search • Real-Time Reports & Charts'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Simplify Your Eye Care Practice',
                        'description' => 'The Optical & Eyecare Management Add-On brings all essential clinic and optical store operations into one centralized system. It efficiently manages patient records, doctor details, prescriptions, appointments, and eyewear orders in a single platform. A real-time dashboard provides instant insights into patient activity, doctor availability, and order status.',
                        'subSections' => [
                            [
                                'title' => 'Patient & Doctor Management',
                                'description' => 'Managing the core members of your practice is simple with dedicated modules for both patients and doctors. The system maintains complete patient profiles, including personal details, medical history, prescriptions, and preferred doctors for quick and accurate access.',
                                'keyPoints' => [
                                    'Maintain detailed patient profiles with complete medical history',
                                    'Access previous prescriptions and treatment records easily anytime',
                                    'Track doctor credentials, consultation fees, and experience accurately',
                                    'Monitor real-time doctor availability and appointment scheduling system'
                                ],
                                'screenshot' => '/packages/workdo/OpticalAndEyeCareCenter/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Prescriptions & Appointment Scheduling',
                                'description' => 'Eye test prescriptions and appointment scheduling are managed through dedicated sections for better control and organization. Prescriptions are linked to specific patients and doctors, with test results, details, and expiry dates stored in one place.',
                                'keyPoints' => [
                                    'Manage eye test prescriptions linked to specific patients',
                                    'Store test results, details, and prescription expiry dates',
                                    'Automatically flag expired prescriptions for timely follow-up actions',
                                    'Schedule appointments with a doctor, date, time, and status'
                                ],
                                'screenshot' => '/packages/workdo/OpticalAndEyeCareCenter/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Eyewear Inventory & Order Management',
                                'description' => 'The Add-On streamlines retail and fulfillment with integrated eyewear inventory and order management. Teams can maintain a complete product catalog, including pricing, stock levels, categories, and details for easy tracking.',
                                'keyPoints' => [
                                    'Manage a complete eyewear catalog with pricing and stock',
                                    'Track inventory levels and product categories efficiently',
                                    'Process patient orders with discounts and payment tracking',
                                    'Automatically record finalized orders as accounting entries'
                                ],
                                'screenshot' => '/packages/workdo/OpticalAndEyeCareCenter/src/marketplace/image3.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Optical & Eyecare Module in Action',
                        'subtitle' => 'See how our comprehensive tools improve your clinic and optical store operations',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Optical & Eyecare Management?',
                        'subtitle' => 'Stay organized, improve patient care, and efficiently track performance',
                        'benefits' => [
                            [
                                'title' => 'Centralized Dashboard',
                                'description' => 'Real-time overview of clinic activity with visual charts tracking appointments, orders, and monthly trends.',
                                'icon' => 'Activity',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Complete Patient Records',
                                'description' => 'Maintain detailed patient profiles with complete medical history and prescription records.',
                                'icon' => 'Users',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Smart Prescription Management',
                                'description' => 'Store test results and automatically flag expired prescriptions for timely follow-up.',
                                'icon' => 'FileText',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Appointment Scheduling',
                                'description' => 'Efficient scheduling system with doctor availability and complete visit details.',
                                'icon' => 'Calendar',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Inventory Control',
                                'description' => 'Track eyewear catalog with pricing, stock levels, and product categories efficiently.',
                                'icon' => 'Package',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Automated Accounting',
                                'description' => 'Finalized orders are automatically recorded as accounting entries for accurate financial records.',
                                'icon' => 'DollarSign',
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