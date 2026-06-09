<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyCertification;
use Workdo\BeautySpaManagement\Models\BeautyTraining;
use Carbon\Carbon;

class DemoBeautyCertificationSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyCertification::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            // Get existing training records to link certifications
            $trainings = BeautyTraining::where('created_by', $userId)->get();
            if ($trainings->isEmpty()) {
                return; // No trainings available to link certifications
            }

            // 30 realistic certification records ordered oldest to newest (6 months)
            $certificationRecords = [
                ['employee_name' => 'Sarah Mitchell', 'certificate_name' => 'Advanced Anti-Aging Facial Specialist', 'issued_date' => Carbon::now()->subDays(180), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Advanced Anti-Aging Facial Techniques'],
                ['employee_name' => 'Maria Rodriguez', 'certificate_name' => 'Certified Hydrafacial Technician', 'issued_date' => Carbon::now()->subDays(174), 'expiry_date' => Carbon::now()->addYears(3), 'training_match' => 'Hydrafacial Certification Program'],
                ['employee_name' => 'Emma Thompson', 'certificate_name' => 'Organic Skincare Therapy Certificate', 'issued_date' => Carbon::now()->subDays(168), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Organic Facial Therapy Workshop'],
                ['employee_name' => 'James Wilson', 'certificate_name' => 'Deep Tissue Massage Therapist', 'issued_date' => Carbon::now()->subDays(162), 'expiry_date' => Carbon::now()->addYears(3), 'training_match' => 'Deep Tissue Massage Mastery'],
                ['employee_name' => 'Lisa Chen', 'certificate_name' => 'Hot Stone Massage Specialist', 'issued_date' => Carbon::now()->subDays(156), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Hot Stone Massage Certification'],
                ['employee_name' => 'Dr. Amanda Foster', 'certificate_name' => 'Prenatal Massage Therapy Certificate', 'issued_date' => Carbon::now()->subDays(150), 'expiry_date' => Carbon::now()->addYears(3), 'training_match' => 'Prenatal Massage Specialist Course'],
                ['employee_name' => 'Roberto Silva', 'certificate_name' => 'Professional Keratin Treatment Specialist', 'issued_date' => Carbon::now()->subDays(144), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Keratin Treatment Professional Training'],
                ['employee_name' => 'Sophie Laurent', 'certificate_name' => 'Advanced Hair Coloring Certificate', 'issued_date' => Carbon::now()->subDays(138), 'expiry_date' => Carbon::now()->addYears(3), 'training_match' => 'Balayage and Highlighting Techniques'],
                ['employee_name' => 'Victoria Adams', 'certificate_name' => 'Bridal Hair Styling Expert', 'issued_date' => Carbon::now()->subDays(132), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Bridal Hair Styling Masterclass'],
                ['employee_name' => 'Nina Patel', 'certificate_name' => 'Gel Nail Extension Professional', 'issued_date' => Carbon::now()->subDays(126), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Gel Nail Extension Certification'],
                ['employee_name' => 'Yuki Tanaka', 'certificate_name' => 'Advanced Nail Art Designer', 'issued_date' => Carbon::now()->subDays(120), 'expiry_date' => Carbon::now()->addYears(3), 'training_match' => 'Advanced Nail Art Design Workshop'],
                ['employee_name' => 'Anastasia Volkov', 'certificate_name' => 'Russian Manicure Specialist', 'issued_date' => Carbon::now()->subDays(114), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Russian Manicure Technique Training'],
                ['employee_name' => 'Dr. Michael Brown', 'certificate_name' => 'Body Contouring Therapy Certificate', 'issued_date' => Carbon::now()->subDays(108), 'expiry_date' => Carbon::now()->addYears(3), 'training_match' => 'Body Contouring and Cellulite Treatment'],
                ['employee_name' => 'Isabella Martinez', 'certificate_name' => 'Aromatherapy Body Wrap Specialist', 'issued_date' => Carbon::now()->subDays(102), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Aromatherapy Body Wrap Certification'],
                ['employee_name' => 'Rachel Green', 'certificate_name' => 'Body Exfoliation Therapy Certificate', 'issued_date' => Carbon::now()->subDays(96), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Exfoliation and Body Polish Techniques'],
                ['employee_name' => 'Priya Sharma', 'certificate_name' => 'Professional Bridal Makeup Artist', 'issued_date' => Carbon::now()->subDays(90), 'expiry_date' => Carbon::now()->addYears(3), 'training_match' => 'Bridal Makeup Artistry Course'],
                ['employee_name' => 'Carlos Rodriguez', 'certificate_name' => 'Airbrush Makeup Specialist', 'issued_date' => Carbon::now()->subDays(84), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Airbrush Makeup Certification'],
                ['employee_name' => 'Alexandra King', 'certificate_name' => 'Special Effects Makeup Artist', 'issued_date' => Carbon::now()->subDays(78), 'expiry_date' => Carbon::now()->addYears(3), 'training_match' => 'Special Effects Makeup Workshop'],
                ['employee_name' => 'Dr. Jennifer Lee', 'certificate_name' => 'Chemical Peel Specialist Certificate', 'issued_date' => Carbon::now()->subDays(72), 'expiry_date' => Carbon::now()->addYears(3), 'training_match' => 'Chemical Peel Certification Program'],
                ['employee_name' => 'Thomas Anderson', 'certificate_name' => 'Microneedling Therapy Specialist', 'issued_date' => Carbon::now()->subDays(66), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Microneedling Therapy Training'],
                ['employee_name' => 'Dr. Patricia White', 'certificate_name' => 'LED Light Therapy Certificate', 'issued_date' => Carbon::now()->subDays(60), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'LED Light Therapy Specialist Course'],
                ['employee_name' => 'Master Liu Wei', 'certificate_name' => 'Professional Reflexology Certificate', 'issued_date' => Carbon::now()->subDays(54), 'expiry_date' => Carbon::now()->addYears(3), 'training_match' => 'Reflexology Certification Program'],
                ['employee_name' => 'Sage Williams', 'certificate_name' => 'Crystal Healing Therapy Certificate', 'issued_date' => Carbon::now()->subDays(48), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Crystal Healing Therapy Workshop'],
                ['employee_name' => 'Master Kenji Nakamura', 'certificate_name' => 'Reiki Level 1 Practitioner', 'issued_date' => Carbon::now()->subDays(42), 'expiry_date' => Carbon::now()->addYears(5), 'training_match' => 'Reiki Energy Healing Level 1'],
                ['employee_name' => 'Margaret Johnson', 'certificate_name' => 'Spa Business Management Certificate', 'issued_date' => Carbon::now()->subDays(36), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Spa Business Management Training'],
                ['employee_name' => 'David Thompson', 'certificate_name' => 'Client Communication Specialist', 'issued_date' => Carbon::now()->subDays(30), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Client Consultation and Communication'],
                ['employee_name' => 'Dr. Helen Carter', 'certificate_name' => 'Health & Safety Compliance Certificate', 'issued_date' => Carbon::now()->subDays(24), 'expiry_date' => Carbon::now()->addYears(1), 'training_match' => 'Hygiene and Sanitation Protocols'],
                ['employee_name' => 'Bella Romano', 'certificate_name' => 'Advanced Lash Extension Specialist', 'issued_date' => Carbon::now()->subDays(18), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Advanced Lash Extension Techniques'],
                ['employee_name' => 'Sophia Garcia', 'certificate_name' => 'Microblading & Brow Shaping Expert', 'issued_date' => Carbon::now()->subDays(12), 'expiry_date' => Carbon::now()->addYears(3), 'training_match' => 'Microblading and Brow Shaping'],
                ['employee_name' => 'Dr. Robert Kim', 'certificate_name' => 'Cryotherapy Treatment Specialist', 'issued_date' => Carbon::now()->subDays(6), 'expiry_date' => Carbon::now()->addYears(2), 'training_match' => 'Cryotherapy and Cold Treatment']
            ];

            foreach ($certificationRecords as $record) {
                // Find matching training by name similarity
                $matchingTraining = $trainings->first(function ($training) use ($record) {
                    return stripos($training->training_name, explode(' ', $record['training_match'])[0]) !== false;
                });

                // If no specific match found, use random training
                if (!$matchingTraining) {
                    $matchingTraining = $trainings->random();
                }

                BeautyCertification::create([
                    'employee_name' => $record['employee_name'],
                    'certificate_name' => $record['certificate_name'],
                    'issued_date' => $record['issued_date']->toDateString(),
                    'expiry_date' => $record['expiry_date']->toDateString(),
                    'training_id' => $matchingTraining->id,
                    'creator_id' => $userId,
                    'created_by' => $userId,
                    'created_at' => $record['issued_date'],
                    'updated_at' => $record['issued_date'],
                ]);
            }
        }
    }
}
