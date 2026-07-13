<?php

namespace Workdo\SuggestionBox\Database\Seeders;

use Workdo\SuggestionBox\Models\SuggestionCategory;
use Illuminate\Database\Seeder;

class DemoSuggestionCategorySeeder extends Seeder
{
    public function run($userId): void
    {
        if (SuggestionCategory::where('created_by', $userId)->exists()) {
            return;
        }

        $categories = [
            ['name' => 'HR', 'color' => '#3B82F6', 'description' => 'Human resources and employee related suggestions'],
            ['name' => 'Operation', 'color' => '#10B981', 'description' => 'Operational processes and workflow improvements'],
            ['name' => 'Product', 'color' => '#F59E0B', 'description' => 'Product features and enhancement suggestions'],
            ['name' => 'General', 'color' => '#8427a8', 'description' => 'General feedback and miscellaneous suggestions'],
            ['name' => 'Technology', 'color' => '#EF4444', 'description' => 'Technology infrastructure and development suggestions'],
            ['name' => 'Security', 'color' => '#8B5CF6', 'description' => 'Security enhancements and vulnerability reports'],
            ['name' => 'Finance', 'color' => '#EC4899', 'description' => 'Financial processes and cost optimization suggestions'],
            ['name' => 'Marketing', 'color' => '#06B6D4', 'description' => 'Marketing strategies and promotional suggestions'],
            ['name' => 'Customer Service', 'color' => '#84CC16', 'description' => 'Customer support and service improvement suggestions'],
            ['name' => 'Training', 'color' => '#F97316', 'description' => 'Employee training and development suggestions'],
            ['name' => 'Quality', 'color' => '#6366F1', 'description' => 'Quality assurance and improvement suggestions'],
            ['name' => 'Innovation', 'color' => '#14B8A6', 'description' => 'Innovation and creative ideas for business growth']
        ];

        foreach ($categories as $index => $category) {
            SuggestionCategory::create([
                'name'          => $category['name'],
                'color'         => $category['color'],
                'description'   => $category['description'],
                'is_active'     => true,
                'display_order' => $index + 1,
                'creator_id'    => $userId,
                'created_by'    => $userId,
            ]);
        }
    }
}