<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\BeautySpaManagement\Models\BeautyContact;
use Carbon\Carbon;

class DemoBeautyContactSeeder extends Seeder
{
    public function run($userId): void
    {
        if (BeautyContact::where('created_by', $userId)->exists()) {
            return; // All three tables contain user data → skip seeding
        }
        if (!empty($userId)) {
            // 15 realistic contact inquiries ordered oldest to newest (6 months)
            $contactRecords = [
                ['name' => 'Emma Thompson', 'email' => 'emma.thompson@gmail.com', 'phone' => '+16195550124', 'subject' => 'Bridal Package Inquiry', 'message' => 'Hi, I am getting married in June and would like to know more about your bridal beauty packages. Could you please send me details about pricing and what services are included? Thank you.', 'date' => Carbon::now()->subDays(180)],
                ['name' => 'Sophia Martinez', 'email' => 'sophia.martinez@yahoo.com', 'phone' => '+14805550117', 'subject' => 'Appointment Availability', 'message' => 'Hello, I would like to schedule a facial treatment for next week. Are there any available slots on weekends? I prefer Saturday mornings if possible. Please let me know your availability.', 'date' => Carbon::now()->subDays(168)],
                ['name' => 'Isabella Garcia', 'email' => 'isabella.garcia@hotmail.com', 'phone' => '+15125550118', 'subject' => 'Gift Certificate Purchase', 'message' => 'I want to purchase a gift certificate for my mother for Mother\'s Day. What denominations do you offer and can I buy it online? She loves spa treatments and massages.', 'date' => Carbon::now()->subDays(156)],
                ['name' => 'Olivia Rodriguez', 'email' => 'olivia.rodriguez@outlook.com', 'phone' => '+15550126', 'subject' => 'Membership Program Details', 'message' => 'Could you provide information about your membership programs? I am interested in regular monthly treatments and would like to know about discounts and benefits for members. Thank you for your time.', 'date' => Carbon::now()->subDays(144)],
                ['name' => 'Ava Wilson', 'email' => 'ava.wilson@gmail.com', 'phone' => '+165550127', 'subject' => 'Skin Consultation Request', 'message' => 'I have sensitive skin and would like to schedule a consultation before booking any treatments. Do you offer complimentary skin analysis? I want to ensure the products are suitable for my skin type.', 'date' => Carbon::now()->subDays(132)],
                ['name' => 'Mia Anderson', 'email' => 'mia.anderson@yahoo.com', 'phone' => '+155501284', 'subject' => 'Group Booking Inquiry', 'message' => 'We are planning a bachelorette party for 6 people and would like to book spa services together. Do you accommodate group bookings and offer any special packages for parties? Please advise availability.', 'date' => Carbon::now()->subDays(120)],
                ['name' => 'Charlotte Taylor', 'email' => 'charlotte.taylor@gmail.com', 'phone' => '+155501212', 'subject' => 'Pregnancy Safe Treatments', 'message' => 'I am currently pregnant and wondering which of your treatments are safe during pregnancy. I am particularly interested in prenatal massage and gentle facial treatments. Could you please provide guidance?', 'date' => Carbon::now()->subDays(108)],
                ['name' => 'Amelia Brown', 'email' => 'amelia.brown@hotmail.com', 'phone' => '+19015550119', 'subject' => 'Cancellation Policy Question', 'message' => 'What is your cancellation policy for appointments? I sometimes have unpredictable work schedule and want to understand the terms before booking. Do you charge cancellation fees for last-minute changes?', 'date' => Carbon::now()->subDays(96)],
                ['name' => 'Harper Davis', 'email' => 'harper.davis@outlook.com', 'phone' => '+1404555012', 'subject' => 'Product Recommendation', 'message' => 'After my recent facial treatment, I loved the products you used. Could you recommend a home skincare routine with similar products? I would like to maintain the results between visits.', 'date' => Carbon::now()->subDays(84)],
                ['name' => 'Evelyn Miller', 'email' => 'evelyn.miller@gmail.com', 'phone' => '+155501321', 'subject' => 'Special Occasion Makeup', 'message' => 'I have a wedding to attend next month and need professional makeup application. Do you offer makeup services for special events? I would also like a trial session beforehand.', 'date' => Carbon::now()->subDays(72)],
                ['name' => 'Abigail Johnson', 'email' => 'abigail.johnson@yahoo.com', 'phone' => '+15035550122', 'subject' => 'Anti-Aging Treatment Options', 'message' => 'I am interested in anti-aging treatments and would like to know what options you offer. Could you provide information about microdermabrasion, chemical peels, and other rejuvenation treatments available?', 'date' => Carbon::now()->subDays(60)],
                ['name' => 'Emily Clark', 'email' => 'emily.clark@gmail.com', 'phone' => '+14805550117', 'subject' => 'Loyalty Program Benefits', 'message' => 'I am a regular customer and heard about your loyalty program. How do I enroll and what benefits do members receive? I visit your spa monthly and would love to earn rewards.', 'date' => Carbon::now()->subDays(48)],
                ['name' => 'Elizabeth Lewis', 'email' => 'elizabeth.lewis@hotmail.com', 'phone' => '+17135550115', 'subject' => 'Couples Spa Package', 'message' => 'My husband and I would like to book a couples spa day for our anniversary. What packages do you offer for couples and do you have private treatment rooms available?', 'date' => Carbon::now()->subDays(36)],
                ['name' => 'Sofia Walker', 'email' => 'sofia.walker@outlook.com', 'phone' => '+12155550114', 'subject' => 'Teen Skincare Services', 'message' => 'Do you offer skincare treatments for teenagers? My 16-year-old daughter is struggling with acne and I think professional treatment would help. What age-appropriate services do you provide?', 'date' => Carbon::now()->subDays(24)],
                ['name' => 'Avery Hall', 'email' => 'avery.hall@gmail.com', 'phone' => '+14155550113', 'subject' => 'Holiday Gift Ideas', 'message' => 'I am looking for holiday gift ideas for my female colleagues. What spa packages or services would you recommend as gifts? I need something suitable for different preferences and budgets.', 'date' => Carbon::now()->subDays(12)]
            ];

            foreach ($contactRecords as $record) {
                BeautyContact::create([
                    'name' => $record['name'],
                    'email' => $record['email'],
                    'phone' => $record['phone'],
                    'subject' => $record['subject'],
                    'message' => $record['message'],
                    'created_by' => $userId,
                    'created_at' => $record['date'],
                    'updated_at' => $record['date'],
                ]);
            }
        }
    }
}