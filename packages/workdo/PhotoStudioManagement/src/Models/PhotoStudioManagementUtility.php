<?php

namespace Workdo\PhotoStudioManagement\Models;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PhotoStudioManagementUtility
{
    public static function GivePermissionToRoles($role_id = null, $rolename = null)
    {
        $staff_permissions = [
            'manage-photo-studio-management',
            'manage-photo-studio-management-dashboard',

            // Camera Kits
            'manage-photo-studio-camera-kit',
            'manage-own-photo-studio-camera-kit',
            'view-photo-studio-camera-kit',

            // Team Members
            'manage-photo-studio-team-members',
            'manage-own-photo-studio-team-members',
            'view-photo-studio-team-members',
         
            // Services
            'manage-photo-studio-service',
            'manage-own-photo-studio-service',
            'view-photo-studio-service',

            // Contacts
            'manage-photo-studio-contacts',
            'view-photo-studio-contacts',

            // Subscribers
            'manage-photo-studio-subscribers',
            'view-photo-studio-subscribers',

            // Appointments
            'manage-photo-studio-appointments',
            'manage-own-photo-studio-appointments',
            'view-photo-studio-appointments',

            // Appointment Payments
            'manage-photo-studio-appointment-payments',
            'manage-own-photo-studio-appointment-payments',
            'view-photo-studio-appointment-payments',
         
        ];

        if ($rolename == 'staff') {
            $role = Role::where('name', 'staff')->where('id', $role_id)->first();
            if ($role) {
                foreach ($staff_permissions as $permission_name) {
                    $permission = Permission::where('name', $permission_name)->first();
                    if (!empty($permission) && !$role->hasPermissionTo($permission_name)) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }
    }
}
