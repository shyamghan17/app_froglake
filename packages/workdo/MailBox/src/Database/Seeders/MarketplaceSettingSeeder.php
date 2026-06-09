<?php

namespace Workdo\MailBox\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/MailBox/src/marketplace/' . $file->getFilename();
                }
            }
        }

        sort($screenshots);

        MarketplaceSetting::firstOrCreate(['module' => 'MailBox'], [
            'module' => 'MailBox',
            'title' => 'MailBox Add-On',
            'subtitle' => 'Professional email management and communication tools for your platform',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'MailBox Add-On for WorkDo Dash',
                        'subtitle' => 'Transform your email management with integrated IMAP/SMTP support and comprehensive folder organization.',
                        'primary_button_text' => 'Install MailBox Add-On',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/MailBox/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'MailBox Add-On',
                        'subtitle' => 'Integrate powerful email management directly into your workflow'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Dedicated MailBox Features',
                        'description' => 'Our mailbox add-on provides complete email management capabilities with IMAP integration, folder organization, and advanced email composition tools for seamless communication workflows.',
                        'subSections' => [
                            [
                                'title' => 'Email Configuration & Quick Setup',
                                'description' => 'Comprehensive email account configuration with quick setup options for popular providers like Gmail, Outlook, Yahoo, and iCloud. Features automatic server detection, connection testing, and multi-account management with secure credential storage.',
                                'keyPoints' => ['Quick Provider Setup', 'Connection Testing', 'Multi-Account Support', 'Secure Credentials'],
                                'screenshot' => '/packages/workdo/MailBox/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Advanced Email Management',
                                'description' => 'Full-featured email client with folder navigation, search functionality, bulk actions, and email organization. Users can manage inbox, sent items, drafts, trash, and custom folders with advanced filtering and sorting capabilities.',
                                'keyPoints' => ['Folder Organization', 'Advanced Search', 'Bulk Actions', 'Email Filtering'],
                                'screenshot' => '/packages/workdo/MailBox/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Email Composition & Communication',
                                'description' => 'Professional email composer with rich formatting, attachment support, CC/BCC functionality, and reply management. Features auto-save, draft management, and seamless integration with SMTP servers for reliable email delivery.',
                                'keyPoints' => ['Rich Text Editor', 'Attachment Support', 'Auto-save Drafts', 'Reply Management'],
                                'screenshot' => '/packages/workdo/MailBox/src/marketplace/image3.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'MailBox Add-On in Action',
                        'subtitle' => 'See how our email management tools streamline communication',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose MailBox Add-On?',
                        'subtitle' => 'Enhance productivity with integrated email management capabilities',
                        'benefits' => [
                            [
                                'title' => 'IMAP Integration',
                                'description' => 'Full IMAP support for real-time email synchronization with your mail server.',
                                'icon' => 'Mail',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Quick Setup',
                                'description' => 'One-click configuration for Gmail, Outlook, Yahoo, and iCloud accounts.',
                                'icon' => 'Settings',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Multi-Account',
                                'description' => 'Manage multiple email accounts with easy switching and organization.',
                                'icon' => 'Users',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Secure Communication',
                                'description' => 'SSL/TLS encryption support for secure email transmission and storage.',
                                'icon' => 'Shield',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Advanced Search',
                                'description' => 'Powerful search functionality across all folders and email content.',
                                'icon' => 'Search',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Folder Management',
                                'description' => 'Complete folder organization with inbox, sent, drafts, and custom folders.',
                                'icon' => 'FolderOpen',
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
