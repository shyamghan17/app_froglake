<?php

namespace Workdo\SuggestionBox\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/SuggestionBox/src/marketplace/' . $file->getFilename();
                }
            }
        }
        
        sort($screenshots);
        
        MarketplaceSetting::firstOrCreate(['module' => 'SuggestionBox'], [
            'module' => 'SuggestionBox',
            'title' => 'Suggestion Box & Feedback Management System',
            'subtitle' => 'Comprehensive suggestion collection and feedback management platform with voting system, category organization, and administrative response capabilities. Empower your organization to gather valuable insights, manage suggestions effectively, and foster continuous improvement through structured feedback processes.',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Suggestion Box Module for WorkDo Dash',
                        'subtitle' => 'Transform your feedback collection with comprehensive suggestion management, voting systems, and administrative response tools for continuous improvement.',
                        'primary_button_text' => 'Install Suggestion Box Module',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'View Features',
                        'secondary_button_link' => '#features',
                        'image' => '/packages/workdo/SuggestionBox/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Suggestion Box Module',
                        'subtitle' => 'Professional feedback management with suggestion collection, voting, and administrative oversight'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Complete Suggestion & Feedback Management Solution',
                        'description' => 'Our suggestion box system delivers comprehensive feedback collection with organized suggestion management, democratic voting systems, category-based organization, and professional administrative response capabilities for enhanced organizational improvement.',
                        'subSections' => [
                            [
                                'title' => 'Admin Dashboard & Response Management',
                                'description' => 'Comprehensive administrative control panel with suggestion oversight, status management, and official response capabilities. Monitor entire suggestion lifecycle with administrative dashboard, review workflow, response management, and decision documentation for professional feedback handling and organizational transparency.',
                                'keyPoints' => [
                                    'Administrative dashboard with complete suggestion overview and management controls',
                                    'Suggestion review workflow with approval, rejection, and status update capabilities',
                                    'Official response system with administrative feedback and decision documentation',
                                    'Status management tools with suggestion lifecycle tracking and progress monitoring',
                                    'Administrative analytics with suggestion metrics and performance insights',
                                    'Response tracking with administrator identification and response timeline management'
                                ],
                                'screenshot' => '/packages/workdo/SuggestionBox/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Suggestion Management & Submission',
                                'description' => 'Complete suggestion collection system with category-based organization, detailed submission tracking, and comprehensive suggestion profiles. Enable users to submit suggestions with title, description, category assignment, and anonymous options for structured feedback collection and organizational improvement initiatives.',
                                'keyPoints' => [
                                    'User-friendly suggestion submission with guided form completion and validation',
                                    'Category-based suggestion organization with customizable classification system',
                                    'Anonymous submission support with privacy protection and user flexibility options',
                                    'Detailed suggestion profiles with title, description, and comprehensive metadata',
                                    'Suggestion status tracking with progress monitoring and lifecycle management',
                                    'Search and filtering capabilities with advanced suggestion discovery tools'
                                ],
                                'screenshot' => '/packages/workdo/SuggestionBox/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'My Suggestions & Personal Management',
                                'description' => 'Personal suggestion management dashboard enabling users to track their submitted suggestions, monitor voting progress, and manage their feedback contributions. View submission history, track suggestion status, and monitor community engagement with personalized suggestion management tools.',
                                'keyPoints' => [
                                    'Personal suggestion dashboard with individual submission tracking and overview',
                                    'Submission history management with chronological listing and status monitoring',
                                    'Vote tracking for personal suggestions with engagement metrics and analytics',
                                    'Status monitoring with real-time updates on suggestion progress and decisions',
                                    'Edit and delete capabilities for pending suggestions with modification controls',
                                    'Personal analytics with submission performance and community response insights'
                                ],
                                'screenshot' => '/packages/workdo/SuggestionBox/src/marketplace/image3.png'
                            ],
                            [
                                'title' => 'Democratic Voting & Community Engagement',
                                'description' => 'Advanced voting system enabling community-driven suggestion evaluation with transparent vote counting, engagement tracking, and popularity metrics. Foster democratic decision-making with vote tallying, suggestion ranking, and community feedback mechanisms for collaborative organizational improvement.',
                                'keyPoints' => [
                                    'Democratic voting system with transparent vote counting and fair evaluation',
                                    'Community-driven suggestion ranking with popularity metrics and trending analysis',
                                    'Vote tracking with individual voter identification and engagement history',
                                    'Real-time vote updates with instant feedback and community participation',
                                    'Voting analytics with participation statistics and engagement measurement',
                                    'Anti-fraud voting mechanisms with duplicate prevention and user authentication'
                                ],
                                'screenshot' => '/packages/workdo/SuggestionBox/src/marketplace/image4.png'
                            ],
                            [
                                'title' => 'Category & Status History Management',
                                'description' => 'Professional category management with customizable suggestion classification and comprehensive status history tracking. Organize suggestions systematically with color-coded categories, display order management, and maintain detailed audit trails of all suggestion status changes and administrative actions.',
                                'keyPoints' => [
                                    'Category management system with color-coded visual organization and customization',
                                    'Hierarchical category structure with display order and priority management controls',
                                    'Status history tracking with complete audit trail and change documentation',
                                    'Administrative action logging with timestamp and user identification records',
                                    'Category analytics with suggestion distribution and usage statistics reporting',
                                    'Status change notifications with automated updates and stakeholder communication'
                                ],
                                'screenshot' => '/packages/workdo/SuggestionBox/src/marketplace/image5.png'
                            ],
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Suggestion Box System in Action',
                        'subtitle' => 'Experience comprehensive feedback management with suggestion collection and democratic voting',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Suggestion Box Module?',
                        'subtitle' => 'Transform organizational improvement with professional feedback collection and management',
                        'benefits' => [
                            [
                                'title' => 'Suggestion Collection',
                                'description' => 'Comprehensive suggestion gathering with category organization and anonymous options.',
                                'icon' => 'MessageSquare',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Democratic Voting',
                                'description' => 'Community-driven evaluation with transparent voting and popularity tracking.',
                                'icon' => 'ThumbsUp',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Category Management',
                                'description' => 'Organized suggestion classification with color-coded category system.',
                                'icon' => 'Tag',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Admin Dashboard',
                                'description' => 'Professional administrative control with response management and oversight.',
                                'icon' => 'Settings',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Analytics & Reports',
                                'description' => 'Comprehensive metrics with suggestion trends and engagement analytics.',
                                'icon' => 'BarChart',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'User Engagement',
                                'description' => 'Enhanced participation with voting systems and feedback mechanisms.',
                                'icon' => 'Users',
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