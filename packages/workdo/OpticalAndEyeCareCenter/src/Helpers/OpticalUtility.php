<?php

namespace Workdo\OpticalAndEyeCareCenter\Helpers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class OpticalUtility
{
    public static function defaultdata($company_id = null)
    {

        $doctorRolePermissions = [
            'manage-dashboard',
            'manage-media',
            'manage-own-media',
            'create-media',
            'download-media',
            'delete-media',
            'manage-media-directories',
            'manage-own-media-directories',
            'create-media-directories',
            'edit-media-directories',
            'delete-media-directories',

            'manage-profile',
            'edit-profile',
            'change-password-profile',

            'manage-messenger',
            'send-messages',
            'view-messages',
            'toggle-favorite-messages',
            'toggle-pinned-messages',

            'manage-optical-and-eye-care-center',
            'manage-optical-dashboard',
            'manage-optical-doctors',
            'manage-own-optical-doctors',
            'view-optical-doctors',
            'create-optical-doctors',
            'edit-optical-doctors',

            'manage-eye-patients',
            'manage-own-eye-patients',
            'view-eye-patients',
            'create-eye-patients',
            'edit-eye-patients',
            'delete-eye-patients',

            'manage-eye-test-prescriptions',
            'manage-own-eye-test-prescriptions',
            'view-eye-test-prescriptions',
            'create-eye-test-prescriptions',
            'edit-eye-test-prescriptions',
            'delete-eye-test-prescriptions',

            'manage-eye-care-appoinments',
            'manage-own-eye-care-appoinments',
            'view-eye-care-appoinments',
            'create-eye-care-appoinments',
            'edit-eye-care-appoinments',
            'delete-eye-care-appoinments',
        ];

        $doctorRole = Role::where('name', 'doctor')->where('created_by', $company_id)->where('guard_name', 'web')->first();
        if (empty($doctorRole)) {
            $doctorRole             = new Role();
            $doctorRole->name       = 'doctor';
            $doctorRole->guard_name = 'web';
            $doctorRole->label      = 'Doctor';
            $doctorRole->editable   = false;
            $doctorRole->created_by = $company_id;
            $doctorRole->save();

           foreach ($doctorRolePermissions as $permission_v) {
                $permission = Permission::where('name', $permission_v)->first();
                if (!empty($permission) && !$doctorRole->hasPermissionTo($permission_v)) {
                    $doctorRole->givePermissionTo($permission);
                }
            }
        }else {
            foreach ($doctorRolePermissions as $permission_v) {
                $permission = Permission::where('name', $permission_v)->where('guard_name', 'web')->first();
                if (!empty($permission) && !$doctorRole->hasPermissionTo($permission_v)) {
                    $doctorRole->givePermissionTo($permission);
                }
            }
        }
    }
}
