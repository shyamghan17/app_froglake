<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class BookingCustomPage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'page_header',
        'page_header_description',
        'content',
        'meta_data',
        'is_active',
        'is_editable',
        'created_by',
        'creator_id'
    ];

    protected $casts = [
        'meta_data' => 'array',
        'is_active' => 'boolean',
        'is_editable' => 'boolean'
    ];

    public static function defaultdata($company_id)
    {
        $pages = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'page_header' => 'Privacy Policy',
                'page_header_description' => 'Your data privacy and security are our priority.',
                'content' => '<p>We collect only essential information to process your bookings, including your name, email address, phone number, and payment details. Your personal details are never shared without your consent. All transactions are encrypted to keep your data safe.</p><p>We use the information we collect to provide, maintain, and improve our services, process bookings, send appointment confirmations, and communicate with you about your bookings.</p><p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy. By using our services, you trust us to protect your privacy responsibly.</p><p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction.</p>',
                'meta_data' => ['keywords' => 'privacy, policy, data protection', 'description' => 'Your data privacy and security are our priority.'],
                'is_active' => true,
                'is_editable' => false,
                'created_by' => $company_id,
                'creator_id' => $company_id
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'page_header' => 'Terms & Conditions',
                'page_header_description' => 'Terms and conditions for using our booking services.',
                'content' => '<p>By accessing and using our booking services, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to these terms, please do not use our services.</p><p>All bookings are subject to availability. We are committed to providing safe and secure services. We reserve the right to cancel or reschedule appointments with reasonable notice. Cancellations must be made at least 24 hours in advance.</p><p>Payment is due at the time of service unless otherwise arranged. We accept various payment methods including cash, credit cards, and online payments. Refunds are processed according to our refund policy.</p><p>Any issues will be handled according to our policy. These terms may be updated periodically to serve you better. We will notify you of any significant changes to these terms.</p>',
                'meta_data' => ['keywords' => 'terms, conditions, service', 'description' => 'Terms and conditions for using our booking services.'],
                'is_active' => true,
                'is_editable' => false,
                'created_by' => $company_id,
                'creator_id' => $company_id
            ]
        ];

        foreach ($pages as $pageData) {
            self::firstOrCreate(
                ['slug' => $pageData['slug'], 'created_by' => $company_id],
                $pageData
            );
        }
    }
}