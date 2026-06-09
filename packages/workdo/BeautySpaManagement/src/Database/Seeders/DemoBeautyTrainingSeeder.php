<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyTraining;
use Carbon\Carbon;

class DemoBeautyTrainingSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyTraining::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            // 30 realistic beauty training records with proper business scenarios
            $trainingRecords = [
                // Advanced Facial Treatments
                ['name' => 'Advanced Anti-Aging Facial Techniques', 'trainer' => 'Dr. Sarah Mitchell', 'date' => Carbon::now()->subDays(180), 'duration' => '3 Days', 'location' => 'Beauty Academy Downtown', 'description' => 'Comprehensive training on advanced anti-aging facial treatments including microdermabrasion, chemical peels, and LED light therapy techniques for mature skin rejuvenation.'],
                ['name' => 'Hydrafacial Certification Program', 'trainer' => 'Maria Rodriguez', 'date' => Carbon::now()->subDays(175), 'duration' => '2 Days', 'location' => 'Spa Training Center', 'description' => 'Professional certification course covering hydrafacial equipment operation, skin analysis, treatment protocols, and client consultation for optimal hydration results.'],
                ['name' => 'Organic Facial Therapy Workshop', 'trainer' => 'Emma Thompson', 'date' => Carbon::now()->subDays(170), 'duration' => '1 Day', 'location' => 'Natural Beauty Institute', 'description' => 'Hands-on workshop focusing on organic and natural facial treatments using botanical ingredients, essential oils, and eco-friendly skincare products for sensitive skin types.'],
                
                // Massage Therapy Training
                ['name' => 'Deep Tissue Massage Mastery', 'trainer' => 'James Wilson', 'date' => Carbon::now()->subDays(165), 'duration' => '4 Days', 'location' => 'Wellness Training Academy', 'description' => 'Intensive training program covering deep tissue massage techniques, anatomy understanding, pressure point therapy, and injury prevention for therapeutic massage practice.'],
                ['name' => 'Hot Stone Massage Certification', 'trainer' => 'Lisa Chen', 'date' => Carbon::now()->subDays(160), 'duration' => '2 Days', 'location' => 'Holistic Spa Institute', 'description' => 'Professional certification in hot stone massage therapy including stone preparation, temperature control, placement techniques, and safety protocols for relaxation treatments.'],
                ['name' => 'Prenatal Massage Specialist Course', 'trainer' => 'Dr. Amanda Foster', 'date' => Carbon::now()->subDays(155), 'duration' => '3 Days', 'location' => 'Maternal Wellness Center', 'description' => 'Specialized training for prenatal massage therapy covering safe positioning, contraindications, pressure modifications, and comfort techniques for expecting mothers.'],
                
                // Hair Styling & Treatments
                ['name' => 'Keratin Treatment Professional Training', 'trainer' => 'Roberto Silva', 'date' => Carbon::now()->subDays(150), 'duration' => '2 Days', 'location' => 'Hair Academy Professional', 'description' => 'Comprehensive keratin treatment training covering product application, processing techniques, aftercare instructions, and client consultation for smooth, frizz-free hair results.'],
                ['name' => 'Balayage and Highlighting Techniques', 'trainer' => 'Sophie Laurent', 'date' => Carbon::now()->subDays(145), 'duration' => '3 Days', 'location' => 'Color Artistry School', 'description' => 'Advanced hair coloring workshop focusing on balayage, foiling techniques, color theory, toning processes, and creating natural-looking highlights for various hair types.'],
                ['name' => 'Bridal Hair Styling Masterclass', 'trainer' => 'Victoria Adams', 'date' => Carbon::now()->subDays(140), 'duration' => '2 Days', 'location' => 'Bridal Beauty Academy', 'description' => 'Specialized training in bridal hairstyling including updos, braiding techniques, hair accessories placement, and long-lasting styling methods for wedding day perfection.'],
                
                // Nail Art & Manicure
                ['name' => 'Gel Nail Extension Certification', 'trainer' => 'Nina Patel', 'date' => Carbon::now()->subDays(135), 'duration' => '3 Days', 'location' => 'Nail Artistry Institute', 'description' => 'Professional gel nail extension course covering application techniques, shaping methods, nail health maintenance, and removal procedures for durable nail enhancements.'],
                ['name' => 'Advanced Nail Art Design Workshop', 'trainer' => 'Yuki Tanaka', 'date' => Carbon::now()->subDays(130), 'duration' => '2 Days', 'location' => 'Creative Nail Studio', 'description' => 'Artistic nail design training featuring hand-painting techniques, 3D nail art, stamping methods, and creative design concepts for unique nail artistry expressions.'],
                ['name' => 'Russian Manicure Technique Training', 'trainer' => 'Anastasia Volkov', 'date' => Carbon::now()->subDays(125), 'duration' => '2 Days', 'location' => 'European Nail Academy', 'description' => 'Precision Russian manicure training covering e-file techniques, cuticle work, nail preparation, and safety protocols for professional dry manicure services.'],
                
                // Body Treatments
                ['name' => 'Body Contouring and Cellulite Treatment', 'trainer' => 'Dr. Michael Brown', 'date' => Carbon::now()->subDays(120), 'duration' => '3 Days', 'location' => 'Medical Spa Training Center', 'description' => 'Advanced body contouring training including radiofrequency treatments, cavitation therapy, lymphatic drainage techniques, and cellulite reduction protocols for body sculpting.'],
                ['name' => 'Aromatherapy Body Wrap Certification', 'trainer' => 'Isabella Martinez', 'date' => Carbon::now()->subDays(115), 'duration' => '2 Days', 'location' => 'Aromatherapy Institute', 'description' => 'Professional body wrap training using essential oils, herbal treatments, detoxification methods, and relaxation techniques for therapeutic body wellness treatments.'],
                ['name' => 'Exfoliation and Body Polish Techniques', 'trainer' => 'Rachel Green', 'date' => Carbon::now()->subDays(110), 'duration' => '1 Day', 'location' => 'Body Care Academy', 'description' => 'Comprehensive body exfoliation training covering scrub techniques, product selection, skin preparation, and moisturizing protocols for smooth, radiant skin results.'],
                
                // Makeup Artistry
                ['name' => 'Bridal Makeup Artistry Course', 'trainer' => 'Priya Sharma', 'date' => Carbon::now()->subDays(105), 'duration' => '4 Days', 'location' => 'Makeup Artistry School', 'description' => 'Professional bridal makeup training covering skin preparation, color matching, long-lasting techniques, photography makeup, and client consultation for wedding beauty services.'],
                ['name' => 'Airbrush Makeup Certification', 'trainer' => 'Carlos Rodriguez', 'date' => Carbon::now()->subDays(100), 'duration' => '2 Days', 'location' => 'Professional Makeup Institute', 'description' => 'Airbrush makeup technique training including equipment operation, foundation application, contouring methods, and maintenance procedures for flawless makeup application.'],
                ['name' => 'Special Effects Makeup Workshop', 'trainer' => 'Alexandra King', 'date' => Carbon::now()->subDays(95), 'duration' => '3 Days', 'location' => 'Creative Makeup Academy', 'description' => 'Advanced special effects makeup training covering prosthetics application, wound simulation, aging techniques, and creative character makeup for theatrical and film applications.'],
                
                // Skincare & Aesthetics
                ['name' => 'Chemical Peel Certification Program', 'trainer' => 'Dr. Jennifer Lee', 'date' => Carbon::now()->subDays(90), 'duration' => '3 Days', 'location' => 'Aesthetic Training Center', 'description' => 'Professional chemical peel training covering peel types, skin assessment, application techniques, post-treatment care, and safety protocols for skin rejuvenation treatments.'],
                ['name' => 'Microneedling Therapy Training', 'trainer' => 'Thomas Anderson', 'date' => Carbon::now()->subDays(85), 'duration' => '2 Days', 'location' => 'Dermal Therapy Institute', 'description' => 'Microneedling certification course including device operation, skin preparation, treatment protocols, aftercare instructions, and contraindications for collagen induction therapy.'],
                ['name' => 'LED Light Therapy Specialist Course', 'trainer' => 'Dr. Patricia White', 'date' => Carbon::now()->subDays(80), 'duration' => '1 Day', 'location' => 'Light Therapy Academy', 'description' => 'LED light therapy training covering wavelength selection, treatment protocols, skin conditions, safety guidelines, and equipment maintenance for phototherapy treatments.'],
                
                // Wellness & Holistic Treatments
                ['name' => 'Reflexology Certification Program', 'trainer' => 'Master Liu Wei', 'date' => Carbon::now()->subDays(75), 'duration' => '4 Days', 'location' => 'Holistic Wellness Institute', 'description' => 'Traditional reflexology training covering pressure point mapping, foot massage techniques, energy flow principles, and therapeutic benefits for overall wellness and relaxation.'],
                ['name' => 'Crystal Healing Therapy Workshop', 'trainer' => 'Sage Williams', 'date' => Carbon::now()->subDays(70), 'duration' => '2 Days', 'location' => 'Crystal Healing Center', 'description' => 'Crystal healing certification covering crystal properties, chakra balancing, energy cleansing techniques, and therapeutic crystal placement for holistic wellness treatments.'],
                ['name' => 'Reiki Energy Healing Level 1', 'trainer' => 'Master Kenji Nakamura', 'date' => Carbon::now()->subDays(65), 'duration' => '3 Days', 'location' => 'Energy Healing Academy', 'description' => 'Reiki Level 1 certification training covering energy channeling techniques, hand positions, self-healing practices, and basic energy healing principles for wellness therapy.'],
                
                // Business & Customer Service
                ['name' => 'Spa Business Management Training', 'trainer' => 'Margaret Johnson', 'date' => Carbon::now()->subDays(60), 'duration' => '2 Days', 'location' => 'Business Training Center', 'description' => 'Comprehensive spa business training covering client management, appointment scheduling, inventory control, staff coordination, and customer service excellence for spa operations.'],
                ['name' => 'Client Consultation and Communication', 'trainer' => 'David Thompson', 'date' => Carbon::now()->subDays(55), 'duration' => '1 Day', 'location' => 'Customer Service Academy', 'description' => 'Professional communication training focusing on client consultation techniques, needs assessment, treatment recommendations, and building long-term client relationships in beauty services.'],
                ['name' => 'Hygiene and Sanitation Protocols', 'trainer' => 'Dr. Helen Carter', 'date' => Carbon::now()->subDays(50), 'duration' => '1 Day', 'location' => 'Health Safety Institute', 'description' => 'Essential hygiene and sanitation training covering sterilization procedures, equipment cleaning, infection control, and health safety regulations for beauty and spa environments.'],
                
                // Recent & Upcoming Training
                ['name' => 'Advanced Lash Extension Techniques', 'trainer' => 'Bella Romano', 'date' => Carbon::now()->subDays(7), 'duration' => '2 Days', 'location' => 'Lash Academy Professional', 'description' => 'Professional eyelash extension training covering volume techniques, isolation methods, adhesive selection, and retention improvement for dramatic lash enhancement services.'],
                ['name' => 'Microblading and Brow Shaping', 'trainer' => 'Sophia Garcia', 'date' => Carbon::now()->subDays(3), 'duration' => '3 Days', 'location' => 'Permanent Makeup Institute', 'description' => 'Microblading certification course covering blade techniques, pigment selection, brow mapping, healing process, and touch-up procedures for semi-permanent eyebrow enhancement.'],
                ['name' => 'Cryotherapy and Cold Treatment', 'trainer' => 'Dr. Robert Kim', 'date' => Carbon::now()->subDays(1), 'duration' => '2 Days', 'location' => 'Advanced Therapy Center', 'description' => 'Cryotherapy training covering cold treatment protocols, equipment operation, safety procedures, and therapeutic benefits for skin tightening and wellness applications.']
            ];

            foreach ($trainingRecords as $index => $record) {
                BeautyTraining::create([
                    'training_name' => $record['name'],
                    'trainer' => $record['trainer'],
                    'date' => $record['date']->toDateString(),
                    'duration' => $record['duration'],
                    'location' => $record['location'],
                    'description' => $record['description'],
                    'creator_id' => $userId,
                    'created_by' => $userId,
                    'created_at' => $record['date'],
                    'updated_at' => $record['date'],
                ]);
            }
        }
    }
}