<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BeautyUtility extends Model
{
    public static function defaultdata($company_id)
    {
        $pages = [
            [
                'title'       => 'Privacy Policy',
                'slug'        => 'privacy-policy',
                'contents'    => '<p>We collect only essential information to process your bookings. Your personal details are never shared without your consent. All transactions are encrypted to keep your data safe. By using our services, you trust us to protect your privacy responsibly.</p>',
                'description' => 'Your data privacy and security are our priority.',
                'is_editable' => false,
                'creator_id'  => $company_id,
                'created_by'  => $company_id,
            ],
            [
                'title'       => 'Terms & Conditions',
                'slug'        => 'terms-conditions',
                'contents'    => '<p>By using our beauty spa services, you agree to these terms. We are committed to providing quality beauty treatments safely and professionally. Any issues or concerns will be handled according to our policy. These terms may be updated periodically to serve you better.</p>',
                'description' => 'Terms and conditions for using our beauty spa services.',
                'is_editable' => false,
                'creator_id'  => $company_id,
                'created_by'  => $company_id,
            ]
        ];

        foreach ($pages as $pageData) {
            BeautyCustomPage::firstOrCreate(
                ['slug' => $pageData['slug'], 'created_by' => $company_id],
                $pageData
            );
        }
    }
    public static function GivePermissionToRoles($role_id = null, $rolename = null)
    {
        $staff_permission = [
            'manage-beauty-spa-management',
            'manage-beauty-spa-dashboard',
            'manage-beauty-services',
            'manage-own-beauty-services'
        ];

        if ($rolename == 'staff') {
            $roles_v = Role::where('name', 'staff')->where('id', $role_id)->first();
            foreach ($staff_permission as $permission_v) {
                $permission = Permission::where('name', $permission_v)->first();
                if (!empty($permission)) {
                    if (!$roles_v->hasPermissionTo($permission_v)) {
                        $roles_v->givePermissionTo($permission);
                    }
                }
            }
        }
    }
}
