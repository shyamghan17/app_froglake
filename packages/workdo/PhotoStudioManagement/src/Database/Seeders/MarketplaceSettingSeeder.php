<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\LandingPage\Models\MarketplaceSetting;
use Illuminate\Support\Facades\File;

class MarketplaceSettingSeeder extends Seeder
{
    public function run()
    {
        $marketplaceDir = __DIR__ . '/../../marketplace';
        $screenshots = [];

        if (File::exists($marketplaceDir)) {
            $files = File::files($marketplaceDir);
            foreach ($files as $file) {
                if (in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
                    $screenshots[] = '/packages/workdo/PhotoStudioManagement/src/marketplace/' . $file->getFilename();
                }
            }
        }

        sort($screenshots);

        MarketplaceSetting::firstOrCreate(['module' => 'PhotoStudioManagement'], [
            'module' => 'PhotoStudioManagement',
            'title' => 'Photo & Studio Management - Complete Photography Studio Solution',
            'subtitle' => 'Transform your photography studio with our comprehensive Photo & Studio Management Package - a complete solution for managing services, appointments, camera kits, team members, and client engagement online',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Photo & Studio Management - Elevate Your Photography Business',
                        'subtitle' => 'Transform your photography studio with our comprehensive Photo & Studio Management Package - a complete solution for managing services, appointments, camera kits, team members, and client engagement online. This powerful system combines an intuitive backend management interface with a fully responsive frontend website, offering seamless appointment booking, equipment tracking, portfolio showcase, payment processing, and real-time analytics. Perfect for photography studios, freelance photographers, and creative agencies who demand professional, efficient, and engaging client experiences.',
                        'primary_button_text' => 'Install Photo Studio Management',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/PhotoStudioManagement/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Photo & Studio Management Module',
                        'subtitle' => 'Perfect for photography studios, freelance photographers, and creative agencies who demand professional, efficient, and engaging client experiences'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Key Features Of Photo & Studio Management',
                        'description' => 'Comprehensive Studio Dashboard & Analytics, Service & Category Management, Appointment Booking System, Camera Kit & Equipment Tracking, Team Member Management, Portfolio & Gallery Showcase, Media & Awards Section, FAQ & Contact Management, Newsletter Subscriber Management, Brand & Frontend Customization.',
                        'subSections' => [
                            [
                                'title' => 'Comprehensive Studio Dashboard & Real-Time Analytics',
                                'description' => 'Monitor your entire studio operation through an interactive dashboard featuring statistical overview cards, visual charts, and comprehensive insights. Track total appointments, team members, services, and revenue with gradient-styled stat cards. Analyze performance through appointment status distribution pie charts, payment status breakdown charts, and recent activity feeds for both appointments and team members. Team members get their own dedicated dashboard showing assigned appointments, status breakdowns, and personal performance metrics.',
                                'keyPoints' => [
                                    'Real-time statistical cards for appointments, revenue, services, and team members',
                                    'Appointment status distribution pie charts',
                                    'Payment status breakdown with visual charts',
                                    'Recent appointments and team member activity feeds',
                                    'Dedicated team member dashboard with personal metrics',
                                    'Copy-to-clipboard studio booking link with customizable welcome card',
                                ],
                                'screenshot' => '/packages/workdo/PhotoStudioManagement/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Service Management & Category Organization',
                                'description' => 'Create and manage photography services with comprehensive details including name, description, pricing, service categories, assigned team members, and linked camera kits. Organize services through a flexible multi-category system with thumbnail uploads and color-coded identification. Filter and search services by category, status, and keywords. Each service can be linked to specific team members and equipment, ensuring the right resources are always available for client bookings.',
                                'keyPoints' => [
                                    'Comprehensive service creation with rich descriptions and pricing',
                                    'Multi-category assignment per service',
                                    'Service category management with thumbnail uploads and color coding',
                                    'Team member and camera kit linking per service',
                                    'Service status controls (active/inactive)',
                                    'Advanced filtering by category, status, and search keywords',
                                ],
                                'screenshot' => '/packages/workdo/PhotoStudioManagement/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Appointment Booking & Team Assignment System',
                                'description' => 'Streamline client appointment management with a full-featured booking system supporting creation, editing, status tracking, and team member assignment. Appointments capture client name, email, mobile number, selected service, booking date range, and pricing. Manage appointment lifecycle through statuses (pending, scheduled, completed, cancelled) and assign multiple team members to each session. Advanced filtering by status, payment status, service, and search terms keeps your schedule organized.',
                                'keyPoints' => [
                                    'Full appointment CRUD with client contact details',
                                    'Date range booking with service and price selection',
                                    'Multi-team-member assignment per appointment',
                                    'Appointment status management (pending, scheduled, completed, cancelled)',
                                    'Payment status tracking per appointment',
                                    'Advanced filtering by status, payment status, service, and search',
                                    'Auto-generated appointment reference numbers',
                                ],
                                'screenshot' => '/packages/workdo/PhotoStudioManagement/src/marketplace/image3.png'
                            ],
                            [
                                'title' => 'Camera Kit & Equipment Inventory Management',
                                'description' => 'Maintain a complete inventory of your photography equipment with the Camera Kit management system. Each kit includes name, image, description, specifications, equipment type classification, and tag-based categorization. Track availability status (available/unavailable/maintenance) and filter equipment by type, status, and search terms. Equipment types and tags are fully customizable, allowing precise organization of cameras, lenses, lighting rigs, accessories, and more.',
                                'keyPoints' => [
                                    'Camera kit creation with images, descriptions, and specifications',
                                    'Equipment type classification (cameras, lenses, lighting, accessories)',
                                    'Tag-based equipment categorization',
                                    'Availability status tracking (available, unavailable, maintenance)',
                                    'Filtering by equipment type, status, and search',
                                    'Equipment linked to services for resource planning',
                                ],
                                'screenshot' => '/packages/workdo/PhotoStudioManagement/src/marketplace/image4.png'
                            ],
                            [
                                'title' => 'Team Member Management & Payment Processing',
                                'description' => 'Build and manage your photography team with detailed profiles including designation, experience years, skills, hourly rate, bio, and active status. Team members are linked to system users and can be assigned to appointments and services. The integrated payment system tracks appointment payments with offline payment recording, payment date, amount, and status management (pending/cleared). Revenue tracking and payment history provide complete financial visibility across all studio operations.',
                                'keyPoints' => [
                                    'Team member profiles with designation, skills, and hourly rates',
                                    'User account linking for team member portal access',
                                    'Appointment payment recording with offline payment support',
                                    'Payment status management (pending, cleared)',
                                    'Revenue tracking and financial reporting',
                                    'Team member active/inactive status controls',
                                ],
                                'screenshot' => '/packages/workdo/PhotoStudioManagement/src/marketplace/image5.png'
                            ],
                            [
                                'title' => 'Complete Frontend Website & Portfolio Showcase',
                                'description' => 'Deliver a professional photography studio website with multi-tenant support using user slug-based routing. The responsive frontend includes a home page with hero banners, service listings, camera kit showcase, and testimonials. Dedicated pages for services, portfolio gallery with gallery-type filtering, appointment booking form, media & awards showcase, FAQ page, and contact page. The gallery section supports type-based filtering for organized portfolio presentation, while the appointment page allows direct client bookings.',
                                'keyPoints' => [
                                    'Multi-tenant architecture with custom user slug routing',
                                    'Responsive design optimized for all devices',
                                    'Portfolio gallery with gallery-type filtering',
                                    'Online appointment booking form with service selection',
                                    'Camera kit showcase with equipment type filtering',
                                    'Media & awards showcase page',
                                    'Newsletter subscription and contact form integration',
                                ],
                                'screenshot' => '/packages/workdo/PhotoStudioManagement/src/marketplace/image6.png'
                            ],
                            [
                                'title' => 'Full Frontend Customization & Brand Settings',
                                'description' => 'Customize every aspect of your studio website through comprehensive system setup tools. Manage brand settings including logo, footer logo, favicon, site title, and footer content. Configure homepage sections: hero banners with multiple slides, about section with images and highlights, gallery section with type-based organization, testimonials with star ratings and client photos, media section, award section, title section, contact section, FAQ section, and footer section. Custom pages with SEO-friendly slugs extend your site with additional content.',
                                'keyPoints' => [
                                    'Brand settings (logo, favicon, site title, footer content)',
                                    'Multi-slide banner section management',
                                    'About, gallery, testimonial, media, and award section customization',
                                    'FAQ and contact section management',
                                    'Custom page creation with SEO-friendly URLs',
                                    'Dashboard welcome card customization',
                                    'Gallery type and equipment type/tag management',
                                ],
                                'screenshot' => '/packages/workdo/PhotoStudioManagement/src/marketplace/image7.png'
                            ],
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Photo & Studio Management Module in Action',
                        'subtitle' => 'See how our comprehensive photography studio platform transforms your business operations',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Photo & Studio Management Module?',
                        'subtitle' => 'Complete Photography Studio Solution with Advanced Features',
                        'benefits' => [
                            [
                                'title' => 'Appointment Booking System',
                                'description' => 'Seamless client appointment management with team assignment and status tracking.',
                                'icon' => 'Calendar',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Studio Dashboard & Analytics',
                                'description' => 'Real-time analytics with statistical cards and visual chart representations.',
                                'icon' => 'BarChart',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Camera Kit & Equipment Tracking',
                                'description' => 'Complete equipment inventory management with availability and type classification.',
                                'icon' => 'Camera',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Payment Management',
                                'description' => 'Offline payment recording with status tracking and revenue reporting.',
                                'icon' => 'CreditCard',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Portfolio & Gallery Showcase',
                                'description' => 'Professional portfolio presentation with gallery-type filtering and media awards.',
                                'icon' => 'Image',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Multi-Tenant Support',
                                'description' => 'Professional frontend website with custom user slug-based routing.',
                                'icon' => 'Globe',
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
