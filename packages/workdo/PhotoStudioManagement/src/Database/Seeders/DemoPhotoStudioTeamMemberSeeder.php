<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Workdo\PhotoStudioManagement\Models\PhotoStudioTeamMember;

class DemoPhotoStudioTeamMemberSeeder extends Seeder
{
    public function run($userId): void
    {
        if (PhotoStudioTeamMember::where('created_by', $userId)->exists()) {
            return;
        }

        $members = [
            ['designation' => 'Lead Photographer', 'experience_year' => 10, 'skills' => 'Portrait, Wedding, Lighting', 'rate_per_hour' => 120.00, 'bio' => 'Award-winning photographer with over a decade of experience in portrait and wedding photography.', 'is_active' => true],
            ['designation' => 'Studio Photographer', 'experience_year' => 6, 'skills' => 'Product, Commercial, Studio Lighting', 'rate_per_hour' => 90.00, 'bio' => 'Specialist in commercial and product photography with a keen eye for detail.', 'is_active' => true],
            ['designation' => 'Videographer', 'experience_year' => 5, 'skills' => 'Cinematography, Drone, Video Editing', 'rate_per_hour' => 100.00, 'bio' => 'Creative videographer skilled in cinematic storytelling and aerial drone footage.', 'is_active' => false],
            ['designation' => 'Photo Editor', 'experience_year' => 4, 'skills' => 'Photoshop, Lightroom, Color Grading', 'rate_per_hour' => 60.00, 'bio' => 'Expert retoucher and color grading specialist delivering polished final images.', 'is_active' => true],
            ['designation' => 'Event Photographer', 'experience_year' => 7, 'skills' => 'Events, Candid, Low Light', 'rate_per_hour' => 85.00, 'bio' => 'Experienced event photographer capturing authentic moments in any lighting condition.', 'is_active' => false],
            ['designation' => 'Fashion Photographer', 'experience_year' => 8, 'skills' => 'Fashion, Editorial, Styling', 'rate_per_hour' => 110.00, 'bio' => 'High-end fashion photographer with editorial experience for top lifestyle brands.', 'is_active' => true],
            ['designation' => 'Newborn Photographer', 'experience_year' => 5, 'skills' => 'Newborn, Family, Posing', 'rate_per_hour' => 95.00, 'bio' => 'Gentle and patient newborn photographer specializing in safe posing and family portraits.', 'is_active' => false],
            ['designation' => 'Real Estate Photographer', 'experience_year' => 4, 'skills' => 'Architecture, Interior, HDR', 'rate_per_hour' => 75.00, 'bio' => 'Skilled in capturing properties with HDR techniques to highlight space and natural light.', 'is_active' => true],
            ['designation' => 'Sports Photographer', 'experience_year' => 6, 'skills' => 'Action, Sports, Fast Shutter', 'rate_per_hour' => 88.00, 'bio' => 'Dynamic sports photographer with expertise in freezing fast-paced action moments.', 'is_active' => false],
            ['designation' => 'Drone Operator', 'experience_year' => 3, 'skills' => 'Aerial, Drone, Landscape', 'rate_per_hour' => 105.00, 'bio' => 'Licensed drone operator delivering stunning aerial perspectives for events and real estate.', 'is_active' => true],
            ['designation' => 'Lighting Technician', 'experience_year' => 5, 'skills' => 'Studio Lighting, Strobe, Reflectors', 'rate_per_hour' => 65.00, 'bio' => 'Expert in setting up complex studio lighting rigs for professional photo and video shoots.', 'is_active' => false],
            ['designation' => 'Social Media Photographer', 'experience_year' => 3, 'skills' => 'Content Creation, Instagram, Branding', 'rate_per_hour' => 70.00, 'bio' => 'Creative content photographer focused on producing scroll-stopping visuals for social media brands.', 'is_active' => true],
        ];

        $users = User::where('created_by', $userId)->emp()->get();

        if ($users->isEmpty()) {
            return;
        }

        $usedUserIds = [];

        foreach ($members as $member) {
            $availableUser = $users->first(fn($u) => !in_array($u->id, $usedUserIds));

            if (!$availableUser) {
                break;
            }

            $usedUserIds[] = $availableUser->id;

            PhotoStudioTeamMember::create([
                'user_id'         => $availableUser->id,
                'designation'     => $member['designation'],
                'experience_year' => $member['experience_year'],
                'skills'          => $member['skills'],
                'rate_per_hour'   => $member['rate_per_hour'],
                'is_active'       => $member['is_active'],
                'bio'             => $member['bio'],
                'creator_id'      => $userId,
                'created_by'      => $userId,
            ]);
        }
    }
}
