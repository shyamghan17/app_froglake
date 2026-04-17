<?php

namespace Workdo\FindGoogleLeads\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/FindGoogleLeads/src/marketplace/' . $file->getFilename();
                }
            }
        }

        sort($screenshots);

        MarketplaceSetting::firstOrCreate(['module' => 'FindGoogleLeads'], [
            'module' => 'FindGoogleLeads',
            'title' => 'FindGoogleLeads Module Marketplace',
            'subtitle' => 'Comprehensive findgoogleleads tools for your applications',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Google Lead Generation & Discovery Module for WorkDo Dash',
                        'subtitle' => 'Transform your lead generation strategy with powerful Google-based prospecting tools and automated lead discovery capabilities.',
                        'primary_button_text' => 'Install FindGoogleLeads Module',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/FindGoogleLeads/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Google Lead Generation & Discovery Module',
                        'subtitle' => 'Enhance your sales pipeline with powerful Google-based lead discovery and prospecting tools'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Comprehensive Google Lead Generation Features',
                        'description' => 'Our Google lead discovery module delivers powerful prospecting capabilities designed to accelerate your sales pipeline and maximize lead conversion rates.',
                        'subSections' => [
                            [
                                'title' => 'Configuration & Setup',
                                'description' => 'Quick and simple integration with Google Places API. Configure your API key and search radius to start discovering high-quality leads. Set up your search parameters including keywords, location, and business types to automate your prospecting process efficiently.',
                                'keyPoints' => ['Simple Google API key setup', 'Customizable search radius', 'Keyword and location targeting', 'Automated lead discovery process'],
                                'screenshot' => '/packages/workdo/FindGoogleLeads/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Manage Find Google Leads',
                                'description' => 'Create and manage your lead search campaigns with ease. Search for businesses using keywords and locations, view all discovered leads in one centralized dashboard. Filter and sort leads by name, keywords, or address. Track the number of contacts found for each search campaign and manage permissions for team collaboration.',
                                'keyPoints' => ['Create lead search campaigns', 'Centralized lead dashboard', 'Advanced filtering and sorting', 'Contact count tracking per campaign'],
                                'screenshot' => '/packages/workdo/FindGoogleLeads/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Google Lead Details',
                                'description' => 'View comprehensive contact information extracted from Google Places including business names, phone numbers, websites, and addresses. Manage individual contacts with options to delete or convert them to leads. Export contact data for CRM integration and outreach campaigns with all essential business details automatically populated.',
                                'keyPoints' => ['Complete contact information extraction', 'Phone numbers and website details', 'Address and business name capture', 'Convert contacts to leads seamlessly'],
                                'screenshot' => '/packages/workdo/FindGoogleLeads/src/marketplace/image3.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Google Lead Generation in Action',
                        'subtitle' => 'Discover how our Google-based lead discovery transforms your sales prospecting',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Google Lead Generation Module?',
                        'subtitle' => 'Accelerate sales growth with intelligent Google-based lead discovery and automation',
                        'benefits' => [
                            [
                                'title' => 'Automated Process',
                                'description' => 'Automate your findgoogleleads workflow to save time and reduce errors.',
                                'icon' => 'Cpu',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Comprehensive Reports',
                                'description' => 'Get detailed reports with metrics and performance data.',
                                'icon' => 'BarChart2',
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
                                'icon' => 'Link',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Quality Management',
                                'description' => 'Maintain high quality with comprehensive management tools.',
                                'icon' => 'Award',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Performance Tracking',
                                'description' => 'Track performance and identify improvements early.',
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
