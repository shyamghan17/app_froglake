<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyServiceType;

class DemoBeautyServiceTypeSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyServiceType::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            // Realistic beauty service type categories
            $serviceTypes = [
                'Facial Treatments',
                'Body Treatments', 
                'Massage Therapy',
                'Hair Services',
                'Nail Care',
                'Makeup Services',
                'Skincare Treatments',
                'Anti-Aging Treatments',
                'Wellness Therapy',
                'Bridal Services',
                'Men\'s Grooming',
                'Spa Packages',
                'Medical Aesthetics',
                'Holistic Treatments',
                'Beauty Consultations'
            ];

            foreach ($serviceTypes as $typeName) {
                BeautyServiceType::create([
                    'name' => $typeName,
                    'creator_id' => $userId,
                    'created_by' => $userId,
                ]);
            }
        }
    }
}