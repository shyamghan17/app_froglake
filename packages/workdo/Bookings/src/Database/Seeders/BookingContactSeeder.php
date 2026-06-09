<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\Bookings\Models\BookingContact;
use App\Models\User;
use Carbon\Carbon;

class BookingContactSeeder extends Seeder
{
    public function run($userId)
    {
        if (!empty($userId)) {
            $this->createContactsForUser($userId);
        }
    }

    private function createContactsForUser($userId)
    {
        if (BookingContact::where('created_by', $userId)->exists()) {
            return;
        }

        $countryCodes = ['+1', '+44', '+91', '+61', '+81', '+49', '+33', '+39', '+55', '+97', '+86', '+7', '+27', '+82', '+34'];

        $contacts = [
            [
                'name' => 'Sarah Mitchell',
                'email' => 'sarah.mitchell@outlook.com',
                'subject' => 'Hair Styling Appointment Request',
                'message' => 'Hello, I would like to schedule a premium hair styling session for next Friday. Do you have any availability in the afternoon?'
            ],
            [
                'name' => 'Marcus Thompson',
                'email' => 'marcus.thompson@gmail.com',
                'subject' => 'Deep Tissue Massage Inquiry',
                'message' => 'I am interested in booking a deep tissue therapy session. Could you provide details about the treatment duration and what to expect?'
            ],
            [
                'name' => 'Isabella Rodriguez',
                'email' => 'isabella.rodriguez@yahoo.com',
                'subject' => 'Bridal Makeup Package Consultation',
                'message' => 'My wedding is in two months and I need a complete bridal makeup package. Can we arrange a trial session to discuss options?'
            ],
            [
                'name' => 'David Chen',
                'email' => 'david.chen@hotmail.com',
                'subject' => 'Service Feedback and Compliment',
                'message' => 'I wanted to express my gratitude for the exceptional anti-aging facial treatment I received yesterday. The results are remarkable and the staff was incredibly professional.'
            ],
            [
                'name' => 'Rachel Williams',
                'email' => 'rachel.williams@gmail.com',
                'subject' => 'Appointment Rescheduling Request',
                'message' => 'Due to an unexpected work commitment, I need to reschedule my Swedish massage appointment from Thursday to the following week. Please let me know available slots.'
            ],
            [
                'name' => 'James Patterson',
                'email' => 'james.patterson@outlook.com',
                'subject' => 'Group Spa Package for Bachelorette Party',
                'message' => 'We are organizing a bachelorette party for eight ladies and would like to book spa services for Saturday afternoon. What group packages do you offer?'
            ],
            [
                'name' => 'Sophia Garcia',
                'email' => 'sophia.garcia@yahoo.com',
                'subject' => 'Nail Services Pricing and Availability',
                'message' => 'Could you please send me detailed pricing for gel nail extensions and classic manicure services? I am also interested in your nail art options.'
            ],
            [
                'name' => 'Robert Johnson',
                'email' => 'robert.johnson@gmail.com',
                'subject' => 'Family Special Occasion Booking',
                'message' => 'My daughter is graduating from university next month. We would like to book hair and makeup services for four family members. Do you offer family packages?'
            ],
            [
                'name' => 'Amanda Davis',
                'email' => 'amanda.davis@hotmail.com',
                'subject' => 'Sensitive Skin Treatment Options',
                'message' => 'I have very sensitive skin and am prone to allergic reactions. Do you offer hypoallergenic facial treatments and what products do you use for sensitive skin?'
            ],
            [
                'name' => 'Christopher Lee',
                'email' => 'christopher.lee@outlook.com',
                'subject' => 'Gift Certificate Purchase Inquiry',
                'message' => 'I would like to purchase a gift certificate for my wife for our anniversary. What are the available denominations and do they have expiration dates?'
            ],
            [
                'name' => 'Victoria Martinez',
                'email' => 'victoria.martinez@gmail.com',
                'subject' => 'Loyalty Program and Membership Benefits',
                'message' => 'As a regular customer who visits monthly for various treatments, I am interested in learning about any loyalty programs or membership benefits you might offer.'
            ],
            [
                'name' => 'Kevin Anderson',
                'email' => 'kevin.anderson@yahoo.com',
                'subject' => 'Men\'s Grooming Services Information',
                'message' => 'What specific services do you offer for men? I am particularly interested in facial treatments, eyebrow shaping, and general grooming services for professionals.'
            ],
            [
                'name' => 'Nicole Taylor',
                'email' => 'nicole.taylor@hotmail.com',
                'subject' => 'Pregnancy-Safe Spa Services',
                'message' => 'I am currently six months pregnant and would love to treat myself to some relaxing spa services. Which treatments are safe during pregnancy and what precautions do you take?'
            ],
            [
                'name' => 'Daniel White',
                'email' => 'daniel.white@gmail.com',
                'subject' => 'Corporate Wellness Event Services',
                'message' => 'Our company is organizing a wellness day for approximately twenty-five employees. Can you provide on-site services or accommodate a large group at your facility?'
            ],
            [
                'name' => 'Grace Thompson',
                'email' => 'grace.thompson@outlook.com',
                'subject' => 'Exceptional Service Experience Review',
                'message' => 'I had to reach out and share how absolutely wonderful my aromatherapy massage experience was today. The therapist was skilled and the entire atmosphere was perfect for relaxation.'
            ]
        ];

        foreach ($contacts as $index => $contact) {
            $createdAt = Carbon::now()->subDays(180 - ($index * 12));

            BookingContact::create([
                'name' => $contact['name'],
                'email' => $contact['email'],
                'phone' => $countryCodes[array_rand($countryCodes)] . mt_rand(1000000000, 9999999999),
                'subject' => $contact['subject'],
                'message' => $contact['message'],
                'created_by' => $userId,
                'creator_id' => $userId,
                'created_at' => $createdAt,
            ]);
        }
    }
}