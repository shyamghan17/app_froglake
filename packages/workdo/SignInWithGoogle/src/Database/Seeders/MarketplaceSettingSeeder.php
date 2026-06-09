<?php

namespace Workdo\SignInWithGoogle\Database\Seeders;

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
                    $screenshots[] = '/packages/workdo/SignInWithGoogle/src/marketplace/' . $file->getFilename();
                }
            }
        }
        
        sort($screenshots);
        
        MarketplaceSetting::firstOrCreate(['module' => 'SignInWithGoogle'], [
            'module' => 'SignInWithGoogle',
            'title' => 'Sign-In With Google Module',
            'subtitle' => 'Enable seamless Google OAuth authentication for your users',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Sign-In With Google Module for WorkDo Dash',
                        'subtitle' => 'Simplify user authentication with secure Google OAuth 2.0 integration and seamless account management.',
                        'primary_button_text' => 'Install Google Sign-In Module',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'Learn More',
                        'secondary_button_link' => '#learn',
                        'image' => '/packages/workdo/SignInWithGoogle/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Google OAuth Authentication',
                        'subtitle' => 'Streamline user login with trusted Google accounts'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Comprehensive Google Authentication',
                        'description' => 'Our Google Sign-In module provides secure OAuth 2.0 integration with advanced user management features.',
                        'subSections' => [
                            [
                                'title' => 'Google Login Settings for Super Admin',
                                'description' => 'Super Admins can configure Google login integration with ease. In the Google Login settings section, enter the Google Client ID, Client Secret, and upload a logo. Additionally, a toggle option is available to enable or disable on the super admin side. The Google login button on the login and register pages gives you full control over its visibility and user access.',
                                'keyPoints' => [],
                                'screenshot' => '/packages/workdo/SignInWithGoogle/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Google Integration',
                                'description' => 'Google Integration enables users to log in with their Google accounts after configuring the Client ID and Secret in Super Admin settings. It supports secure single sign-on (SSO), offering a seamless, password-free login experience. This simplifies access and enhances security. Organizations also benefit from consistent identity management across Google-connected tools.',
                                'keyPoints' => [],
                                'screenshot' => '/packages/workdo/SignInWithGoogle/src/marketplace/image2.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Google Sign-In Module in Action',
                        'subtitle' => 'See how Google authentication enhances user experience',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Google Sign-In Module?',
                        'subtitle' => 'Enhance security and user experience with trusted Google authentication',
                        'benefits' => [
                            [
                                'title' => 'Admin Configuration',
                                'description' => 'Easy setup with Client ID, Secret, and toggle control for visibility.',
                                'icon' => 'Settings',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Secure SSO',
                                'description' => 'Password-free login with Google OAuth 2.0 single sign-on integration.',
                                'icon' => 'Shield',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Identity Management',
                                'description' => 'Consistent user management across Google-connected tools and services.',
                                'icon' => 'Users',
                                'color' => 'purple'
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