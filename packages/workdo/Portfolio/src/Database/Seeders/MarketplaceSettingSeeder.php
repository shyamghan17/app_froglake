<?php

namespace Workdo\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\LandingPage\Models\MarketplaceSetting;
use Illuminate\Support\Facades\File;

class MarketplaceSettingSeeder extends Seeder
{
    public function run()
    {
        // Get all available screenshots from marketplace directory
        $marketplaceDir = __DIR__ . '/../../marketplace';
        $screenshots    = [];

        if (File::exists($marketplaceDir)) {
            $files = File::files($marketplaceDir);
            foreach ($files as $file) {
                if (in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
                    $screenshots[] = '/packages/workdo/Portfolio/src/marketplace/' . $file->getFilename();
                }
            }
        }

        sort($screenshots);

        MarketplaceSetting::firstOrCreate(['module' => 'Portfolio'], [
            'module'          => 'Portfolio',
            'title'           => 'Portfolio Module',
            'subtitle'        => 'Build a strong and organized online presence for professionals, freelancers, and agencies',
            'config_sections' => [
                'sections'           => [
                    'hero'        => [
                        'variant'               => 'hero1',
                        'title'                 => 'Portfolio Module for WorkDo Dash',
                        'subtitle'              => 'The Portfolio Module is designed to help professionals, freelancers, and agencies build a strong and organized online presence. It allows users to showcase their work with detailed portfolio entries, complete with images, contact details, descriptions, and supporting documents.',
                        'primary_button_text'   => 'Install Portfolio Module',
                        'primary_button_link'   => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image'                 => '/packages/workdo/Portfolio/src/marketplace/hero.png'
                    ],
                    'modules'     => [
                        'variant'  => 'modules1',
                        'title'    => 'Portfolio Module',
                        'subtitle' => 'You can also upload attachments to support your portfolio, making it easy to share certificates, documents, or project files.'
                    ],
                    'dedication'  => [
                        'variant'     => 'dedication1',
                        'title'       => 'Professional Portfolio Features',
                        'description' => 'Create comprehensive project showcases with complete control over visibility and sharing, organized with clear categorization.',
                        'subSections' => [
                            [
                                'title'       => 'Build Comprehensive Project Showcases',
                                'description' => 'With this product, you can create detailed portfolio entries that include personal information like name, role, contact email, experience, photo, education, comprehensive work details like Project title, description, category, client, URLs, skills used, timeline, budget, industry, rich overview content, media galleries with images and videos, contact sections, and unlimited custom sections.',
                                'keyPoints'   => ['Personal information management', 'Comprehensive work details', 'Rich overview content', 'Media galleries with images and videos'],
                                'screenshot'  => '/packages/workdo/Portfolio/src/marketplace/image1.png'
                            ],
                            [
                                'title'       => 'Categorize Your Work Clearly',
                                'description' => 'To ensure a neat and organized structure, the product includes a built-in system for managing portfolio categories with active/inactive status controls. You can create multiple categories, each defined by a name and description, to help classify your work based on project type, client, industry, or any other criteria. Only active categories appear in the dropdown when creating portfolio entries, ensuring clean organization.',
                                'keyPoints'   => ['Built-in category management system', 'Active/inactive status controls', 'Multiple classification criteria', 'Clean organization with dropdown filtering'],
                                'screenshot'  => '/packages/workdo/Portfolio/src/marketplace/image2.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant'  => 'screenshots1',
                        'title'    => 'Portfolio Module in Action',
                        'subtitle' => 'See how our portfolio tools help build professional online presence',
                        'images'   => $screenshots
                    ],
                    'why_choose'  => [
                        'variant'  => 'whychoose1',
                        'title'    => 'Why Choose Portfolio Module?',
                        'subtitle' => 'Build a strong and organized online presence with professional portfolio management',
                        'benefits' => [
                            [
                                'title'       => 'Professional Showcase',
                                'description' => 'Create detailed portfolio entries with images, descriptions, and supporting documents.',
                                'icon'        => 'Eye',
                                'color'       => 'blue'
                            ],
                            [
                                'title'       => 'Complete Control',
                                'description' => 'Toggle switches to show or hide content with complete flexibility over presentation.',
                                'icon'        => 'Settings',
                                'color'       => 'green'
                            ],
                            [
                                'title'       => 'Easy Sharing',
                                'description' => 'Generate shareable links for professional frontend showcase pages.',
                                'icon'        => 'Share2',
                                'color'       => 'purple'
                            ],
                            [
                                'title'       => 'Organized Structure',
                                'description' => 'Built-in category management system for neat and organized portfolios.',
                                'icon'        => 'FolderOpen',
                                'color'       => 'red'
                            ],
                            [
                                'title'       => 'Responsive Design',
                                'description' => 'Professional frontend with hero sections, navigation tabs, and contact cards.',
                                'icon'        => 'Smartphone',
                                'color'       => 'yellow'
                            ],
                            [
                                'title'       => 'Attachment Support',
                                'description' => 'Upload certificates, documents, or project files to support your portfolio.',
                                'icon'        => 'Paperclip',
                                'color'       => 'indigo'
                            ]
                        ]
                    ]
                ],
                'section_visibility' => [
                    'header'      => true,
                    'hero'        => true,
                    'modules'     => true,
                    'dedication'  => true,
                    'screenshots' => true,
                    'why_choose'  => true,
                    'cta'         => true,
                    'footer'      => true
                ],
                'section_order'      => ['header', 'hero', 'modules', 'dedication', 'screenshots', 'why_choose', 'cta', 'footer']
            ]
        ]);
    }
}
