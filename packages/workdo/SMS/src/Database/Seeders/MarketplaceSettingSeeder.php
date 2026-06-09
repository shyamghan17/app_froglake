<?php

namespace Workdo\SMS\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/SMS/src/marketplace/' . $file->getFilename();
                }
            }
        }
        
        sort($screenshots);
        
        MarketplaceSetting::firstOrCreate(['module' => 'SMS'], [
            'module' => 'SMS',
            'title' => 'SMS - Universal Text Messaging Platform',
            'subtitle' => 'Multi-provider SMS integration with automated messaging and global delivery capabilities',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'SMS - Master Global Text Communication Excellence',
                        'subtitle' => 'Revolutionize your text messaging strategy with comprehensive SMS integration supporting multiple providers, automated campaigns, and global delivery networks. Connect your business systems with powerful SMS gateways to deliver instant notifications, marketing messages, and critical alerts with guaranteed delivery rates and real-time tracking across worldwide mobile networks.',
                        'primary_button_text' => 'Install SMS Module',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/SMS/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'SMS Module',
                        'subtitle' => 'Enhance your communication workflow with powerful SMS integration and automated messaging'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Enhancing Business Efficiency with SMS Notifications',
                        'description' => 'This integration enables businesses to stay connected with real-time SMS notifications across multiple Add-Ons. Whether managing hospitals, beverage production, music institutes, or mobile services, automated alerts streamline communication and ensure timely updates. From performance tracking to equipment maintenance. By integrating SMS, businesses can boost productivity and keep operations running smoothly without manual follow-ups.',
                        'subSections' => [
                            [
                                'title' => 'Introducing SMS Integration within Dash SaaS',
                                'description' => 'Introducing SMS Integration within Dash SaaS, a groundbreaking feature designed to simplify your communication strategy. With this new addition, notifying individuals via SMS has never been easier. Whether you\'re sending updates, alerts, or reminders, Dash SaaS provides a seamless experience, allowing you to select a category and send messages effortlessly through a range of reliable SMS gateways. Our platform integrates with industry-leading providers such as AWS, Twilio, Clockwork, Melipayamak, Kavenegar, and SMS Gateway, ensuring your messages are delivered promptly and efficiently.',
                                'keyPoints' => [],
                                'screenshot' => '/packages/workdo/SMS/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'SMS Notification With Workdo Dash',
                                'description' => 'You can receive instant notifications of important updates, notifications, and alerts right in your mobile device via SMS. Stay informed about crucial events wherever you are, ensuring you never miss an important message. You can use Slack effectively. First of all, just input your SMS gateway information in the settings page, and then next you can turn on/ off notifications as per your choices.',
                                'keyPoints' => [],
                                'screenshot' => '/packages/workdo/SMS/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Key Features of SMS Integration',
                                'description' => 'With the SMS Add-On for WorkDo Dash, your team can stay informed and collaborate efficiently. Receive real-time notifications on updates and assignments, ensuring everyone remains in sync. Automated alerts help you act quickly on new responsibilities, while customizable channel notifications let you tailor updates for specific teams. Track progress effortlessly with status updates sent directly to SMS, and use built-in messaging tools to collaborate seamlessly.',
                                'keyPoints' => [],
                                'screenshot' => '/packages/workdo/SMS/src/marketplace/image3.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'SMS Module in Action',
                        'subtitle' => 'See how our comprehensive SMS platform transforms your global text messaging strategy',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose SMS Module?',
                        'subtitle' => 'Improve efficiency with comprehensive SMS integration and global messaging capabilities',
                        'benefits' => [
                            [
                                'title' => 'Real-Time SMS Notifications',
                                'description' => 'Stay connected with real-time SMS notifications across multiple Add-Ons and business operations.',
                                'icon' => 'Bell',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Streamlined Communication',
                                'description' => 'Automated alerts streamline communication and ensure timely updates without manual follow-ups.',
                                'icon' => 'MessageSquare',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Enhanced Productivity',
                                'description' => 'Boost productivity and keep operations running smoothly with automated SMS integration.',
                                'icon' => 'TrendingUp',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Multiple Gateway Support',
                                'description' => 'Integrates with AWS, Twilio, Clockwork, Melipayamak, Kavenegar, and SMS Gateway providers.',
                                'icon' => 'Zap',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Instant Mobile Alerts',
                                'description' => 'Receive instant notifications on your mobile device via SMS, ensuring you never miss important messages.',
                                'icon' => 'Smartphone',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Team Collaboration',
                                'description' => 'Real-time notifications on updates and assignments ensure everyone remains in sync.',
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