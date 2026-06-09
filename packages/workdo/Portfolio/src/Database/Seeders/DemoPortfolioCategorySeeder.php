<?php

namespace Workdo\Portfolio\Database\Seeders;

use Workdo\Portfolio\Models\PortfolioCategory;
use Illuminate\Database\Seeder;

class DemoPortfolioCategorySeeder extends Seeder
{
    public function run($userId): void
    {
        if (PortfolioCategory::where('created_by', $userId)->exists()) {
            return;
        }

        $categories = [
            [
                'name'        => 'Web Design',
                'description' => 'Projects focused on creating and optimizing responsive and user-friendly websites.',
            ],
            [
                'name'        => 'Mobile App Development',
                'description' => 'Applications built for Android and iOS platforms with modern UX/UI design.',
            ],
            [
                'name'        => 'Brand Identity',
                'description' => 'Logo, color palette, and branding material for a cohesive brand identity.',
            ],
            [
                'name'        => 'UI/UX Design',
                'description' => 'User experience and interface design for web and mobile applications.',
            ],
            [
                'name'        => 'E-commerce Solutions',
                'description' => 'Custom online store development and payment gateway integrations.',
            ],
            [
                'name'        => 'Digital Marketing',
                'description' => 'Campaigns for SEO, social media marketing, and online brand promotion.',
            ],
            [
                'name'        => 'Photography',
                'description' => 'Professional photoshoots and editing for creative projects and products.',
            ],
            [
                'name'        => 'Video Production',
                'description' => 'Creative video content including editing, motion graphics, and storytelling.',
            ],
            [
                'name'        => 'Graphic Design',
                'description' => 'Visual design for posters, brochures, and digital media campaigns.',
            ],
            [
                'name'        => 'Corporate Projects',
                'description' => 'Enterprise-level projects involving business solutions and integrations.',
            ],
            [
                'name'        => '3D Modeling',
                'description' => 'Creation of realistic 3D models and renders for product visualization.',
            ],
            [
                'name'        => 'Game Design',
                'description' => 'Concepts, character design, and interfaces for game development.',
            ],
            [
                'name'        => 'Content Writing',
                'description' => 'Creative and SEO-friendly content for websites, blogs, and marketing material.',
            ],
            [
                'name'        => 'Illustration',
                'description' => 'Custom digital illustrations for storytelling and brand enhancement.',
            ],
            [
                'name'        => 'Print Design',
                'description' => 'Designs for business cards, packaging, posters, and other print materials.',
            ],
        ];

        $inactiveIndexes = [5, 8, 11, 13];

        foreach ($categories as $index => $category) {
            PortfolioCategory::create([
                'name'        => $category['name'],
                'description' => $category['description'],
                'is_active'   => !in_array($index, $inactiveIndexes), // 4 categories inactive
                'creator_id'  => $userId,
                'created_by'  => $userId,
            ]);
        }
    }
}
