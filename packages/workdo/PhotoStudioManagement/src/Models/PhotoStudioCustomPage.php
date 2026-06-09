<?php

namespace Workdo\PhotoStudioManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhotoStudioCustomPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'contents',
        'description',
        'enable_page_footer',
        'is_editable',
        'creator_id',
        'created_by',
    ];

    protected $casts = [
        'is_editable' => 'boolean',
    ];

    public static function defaultdata($company_id)
    {
        $pages = [
            [
                'title'              => 'Privacy Policy',
                'slug'               => 'privacy-policy',
                'enable_page_footer' => 'on',
                'is_editable'        => false,
                'contents'           => '<p>We collect only essential information to process your photo studio bookings. Your personal details are never shared without your consent. All transactions are encrypted to keep your data safe. By using our services, you trust us to protect your privacy responsibly.</p>',
                'description'        => 'Your data privacy and security are our priority.',
                'creator_id'         => $company_id,
                'created_by'         => $company_id,
            ],
            [
                'title'              => 'Terms & Conditions',
                'slug'               => 'terms-conditions',
                'enable_page_footer' => 'on',
                'is_editable'        => false,
                'contents'           => '<p>By using our photo studio management services, you agree to these terms. We are committed to providing quality studio services safely and on time. Any issues will be handled according to our policy. These terms may be updated periodically to serve you better.</p>',
                'description'        => 'Terms and conditions for using our photo studio services.',
                'creator_id'         => $company_id,
                'created_by'         => $company_id,
            ],
        ];

        foreach ($pages as $pageData) {
            self::firstOrCreate(
                ['slug' => $pageData['slug'], 'created_by' => $company_id],
                $pageData
            );
        }
    }
}
