<?php

namespace Workdo\Rotas\Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class PermissionTableSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();
        Artisan::call('cache:clear');

        $permission = [
            ['name' => 'manage-rotas', 'module' => 'rotas', 'label' => 'Manage Rotas'],
            ['name' => 'manage-any-rotas', 'module' => 'rotas', 'label' => 'Manage All Rotas'],
            ['name' => 'manage-own-rotas', 'module' => 'rotas', 'label' => 'Manage Own Rotas'],
            ['name' => 'create-rotas', 'module' => 'rotas', 'label' => 'Create Rotas'],
            ['name' => 'edit-rotas', 'module' => 'rotas', 'label' => 'Edit Rotas'],
            ['name' => 'delete-rotas', 'module' => 'rotas', 'label' => 'Delete Rotas'],            
            ['name' => 'download-rotas', 'module' => 'rotas', 'label' => 'Download Rotas'],
            ['name' => 'share-rotas', 'module' => 'rotas', 'label' => 'Share Rotas'],
            ['name' => 'publish-rotas', 'module' => 'rotas', 'label' => 'Publish Rotas'],
            ['name' => 'send-mail-rotas', 'module' => 'rotas', 'label' => 'Send Mail Rotas'],            

            ['name' => 'manage-rotas-dashboard', 'module' => 'dashboard', 'label' => 'Manage Rotas Dashboard'],
            
            ['name' => 'manage-rotas-system-setup', 'module' => 'settings', 'label' => 'Manage Rotas System Setup'],
            ['name' => 'manage-rotas-settings', 'module' => 'settings', 'label' => 'Manage Rotas Settings'],
            ['name' => 'edit-rotas-settings', 'module' => 'settings', 'label' => 'Edit Rotas Settings'],
            ['name' => 'manage-work-schedule-settings', 'module' => 'settings', 'label' => 'Manage Rotas Work Schedule Settings'],
            ['name' => 'edit-work-schedule-settings', 'module' => 'settings', 'label' => 'Edit Rotas Work Schedule Settings'],

            // Branche management
            ['name' => 'manage-rotas-branches', 'module' => 'branches', 'label' => 'Manage Branches'],
            ['name' => 'manage-any-rotas-branches', 'module' => 'branches', 'label' => 'Manage All Rotas Branches'],
            ['name' => 'manage-own-rotas-branches', 'module' => 'branches', 'label' => 'Manage Own Rotas Branches'],
            ['name' => 'create-rotas-branches', 'module' => 'branches', 'label' => 'Create Rotas Branches'],
            ['name' => 'edit-rotas-branches', 'module' => 'branches', 'label' => 'Edit Rotas Branches'],
            ['name' => 'delete-rotas-branches', 'module' => 'branches', 'label' => 'Delete Rotas Branches'],

            // Departments management
            ['name' => 'manage-rotas-departments', 'module' => 'departments', 'label' => 'Manage Departments'],
            ['name' => 'manage-any-rotas-departments', 'module' => 'departments', 'label' => 'Manage All Rotas Departments'],
            ['name' => 'manage-own-rotas-departments', 'module' => 'departments', 'label' => 'Manage Own Rotas Departments'],
            ['name' => 'create-rotas-departments', 'module' => 'departments', 'label' => 'Create Rotas Departments'],
            ['name' => 'edit-rotas-departments', 'module' => 'departments', 'label' => 'Edit Rotas Departments'],
            ['name' => 'delete-rotas-departments', 'module' => 'departments', 'label' => 'Delete Rotas Departments'],
            
            // Designation management
            ['name' => 'manage-rotas-designations', 'module' => 'designations', 'label' => 'Manage Designations'],
            ['name' => 'manage-any-rotas-designations', 'module' => 'designations', 'label' => 'Manage All Rotas Designations'],
            ['name' => 'manage-own-rotas-designations', 'module' => 'designations', 'label' => 'Manage Own Rotas Designations'],
            ['name' => 'create-rotas-designations', 'module' => 'designations', 'label' => 'Create Rotas Designations'],
            ['name' => 'edit-rotas-designations', 'module' => 'designations', 'label' => 'Edit Rotas Designations'],
            ['name' => 'delete-rotas-designations', 'module' => 'designations', 'label' => 'Delete Rotas Designations'],
            
            // Leave Type management
            ['name' => 'manage-rotas-leave-types', 'module' => 'leave-types', 'label' => 'Manage Leave Types'],
            ['name' => 'manage-any-rotas-leave-types', 'module' => 'leave-types', 'label' => 'Manage All Rotas Leave Types'],
            ['name' => 'manage-own-rotas-leave-types', 'module' => 'leave-types', 'label' => 'Manage Own Rotas Leave Types'],
            ['name' => 'view-rotas-leave-types', 'module' => 'leave-types', 'label' => 'View Rotas Leave Types'],
            ['name' => 'create-rotas-leave-types', 'module' => 'leave-types', 'label' => 'Create Rotas Leave Types'],
            ['name' => 'edit-rotas-leave-types', 'module' => 'leave-types', 'label' => 'Edit Rotas Leave Types'],
            ['name' => 'delete-rotas-leave-types', 'module' => 'leave-types', 'label' => 'Delete Rotas Leave Types'],
            
            // Employee DocumentType management
            ['name' => 'manage-rotas-employee-document-types', 'module' => 'employee-document-types', 'label' => 'Manage Employee Document Types'],
            ['name' => 'manage-any-rotas-employee-document-types', 'module' => 'employee-document-types', 'label' => 'Manage All Employee Document Types'],
            ['name' => 'manage-own-rotas-employee-document-types', 'module' => 'employee-document-types', 'label' => 'Manage Own Employee Document Types'],
            ['name' => 'create-rotas-employee-document-types', 'module' => 'employee-document-types', 'label' => 'Create Employee Document Types'],
            ['name' => 'edit-rotas-employee-document-types', 'module' => 'employee-document-types', 'label' => 'Edit Employee Document Types'],
            ['name' => 'delete-rotas-employee-document-types', 'module' => 'employee-document-types', 'label' => 'Delete Employee Document Types'],

            // Employee management
            ['name' => 'manage-rotas-employees', 'module' => 'employees', 'label' => 'Manage Employees'],
            ['name' => 'manage-any-rotas-employees', 'module' => 'employees', 'label' => 'Manage All Rotas Employees'],
            ['name' => 'manage-own-rotas-employees', 'module' => 'employees', 'label' => 'Manage Own Rotas Employees'],
            ['name' => 'view-rotas-employees', 'module' => 'employees', 'label' => 'View Rotas Employees'],
            ['name' => 'create-rotas-employees', 'module' => 'employees', 'label' => 'Create Rotas Employees'],
            ['name' => 'edit-rotas-employees', 'module' => 'employees', 'label' => 'Edit Rotas Employees'],
            ['name' => 'delete-rotas-employees', 'module' => 'employees', 'label' => 'Delete Rotas Employees'],

            // Leave Application management
            ['name' => 'manage-rotas-leave-applications', 'module' => 'leave-applications', 'label' => 'Manage Leave Applications'],
            ['name' => 'manage-any-rotas-leave-applications', 'module' => 'leave-applications', 'label' => 'Manage All Leave Applications'],
            ['name' => 'manage-own-rotas-leave-applications', 'module' => 'leave-applications', 'label' => 'Manage Own Leave Applications'],
            ['name' => 'manage-rotas-leave-status', 'module' => 'leave-applications', 'label' => 'Manage Leave Status'],
            ['name' => 'view-rotas-leave-applications', 'module' => 'leave-applications', 'label' => 'View Leave Applications'],
            ['name' => 'create-rotas-leave-applications', 'module' => 'leave-applications', 'label' => 'Create Leave Applications'],
            ['name' => 'edit-rotas-leave-applications', 'module' => 'leave-applications', 'label' => 'Edit Leave Applications'],
            ['name' => 'delete-rotas-leave-applications', 'module' => 'leave-applications', 'label' => 'Delete Leave Applications'],

            // Shift management
            ['name' => 'manage-rotas-shifts', 'module' => 'shifts', 'label' => 'Manage Shifts'],
            ['name' => 'manage-any-rotas-shifts', 'module' => 'shifts', 'label' => 'Manage All Shifts'],
            ['name' => 'manage-own-rotas-shifts', 'module' => 'shifts', 'label' => 'Manage Own Shifts'],
            ['name' => 'create-rotas-shifts', 'module' => 'shifts', 'label' => 'Create Shifts'],
            ['name' => 'edit-rotas-shifts', 'module' => 'shifts', 'label' => 'Edit Shifts'],
            ['name' => 'delete-rotas-shifts', 'module' => 'shifts', 'label' => 'Delete Shifts'],

            // AnnouncementCategory management
            ['name' => 'manage-rotas-announcement-categories', 'module' => 'announcement-categories', 'label' => 'Manage AnnouncementCategories'],
            ['name' => 'manage-any-rotas-announcement-categories', 'module' => 'announcement-categories', 'label' => 'Manage All AnnouncementCategories'],
            ['name' => 'manage-own-rotas-announcement-categories', 'module' => 'announcement-categories', 'label' => 'Manage Own AnnouncementCategories'],
            ['name' => 'create-rotas-announcement-categories', 'module' => 'announcement-categories', 'label' => 'Create AnnouncementCategories'],
            ['name' => 'edit-rotas-announcement-categories', 'module' => 'announcement-categories', 'label' => 'Edit AnnouncementCategories'],
            ['name' => 'delete-rotas-announcement-categories', 'module' => 'announcement-categories', 'label' => 'Delete AnnouncementCategories'],

            // Announcement management
            ['name' => 'manage-rotas-announcements', 'module' => 'announcements', 'label' => 'Manage Announcements'],
            ['name' => 'manage-any-rotas-announcements', 'module' => 'announcements', 'label' => 'Manage All Announcements'],
            ['name' => 'manage-own-rotas-announcements', 'module' => 'announcements', 'label' => 'Manage Own Announcements'],
            ['name' => 'manage-rotas-announcements-status', 'module' => 'announcements', 'label' => 'Manage Announcement Status'],
            ['name' => 'view-rotas-announcements', 'module' => 'announcements', 'label' => 'View Announcements'],
            ['name' => 'create-rotas-announcements', 'module' => 'announcements', 'label' => 'Create Announcements'],
            ['name' => 'edit-rotas-announcements', 'module' => 'announcements', 'label' => 'Edit Announcements'],
            ['name' => 'delete-rotas-announcements', 'module' => 'announcements', 'label' => 'Delete Announcements'],

            // Leave Balace  management
            ['name' => 'manage-rotas-leave-balance', 'module' => 'leave-balance', 'label' => 'View Leave Balance'],
            ['name' => 'manage-any-rotas-leave-balance', 'module' => 'leave-balance', 'label' => 'Manage All Leave Balance'],
            ['name' => 'manage-own-rotas-leave-balance', 'module' => 'leave-balance', 'label' => 'Manage Own Leave Balance'],

            // Availability management
            ['name' => 'manage-rotas-availabilities', 'module' => 'availabilities', 'label' => 'Manage Availabilities'],
            ['name' => 'manage-any-rotas-availabilities', 'module' => 'availabilities', 'label' => 'Manage All Availabilities'],
            ['name' => 'manage-own-rotas-availabilities', 'module' => 'availabilities', 'label' => 'Manage Own Availabilities'],
            ['name' => 'create-rotas-availabilities', 'module' => 'availabilities', 'label' => 'Create Availabilities'],
            ['name' => 'edit-rotas-availabilities', 'module' => 'availabilities', 'label' => 'Edit Availabilities'],
            ['name' => 'delete-rotas-availabilities', 'module' => 'availabilities', 'label' => 'Delete Availabilities'],

            // Work Schedule management
            ['name' => 'manage-rotas-work-schedules', 'module' => 'work-schedules', 'label' => 'Manage Work Schedules'],
            ['name' => 'manage-any-rotas-work-schedules', 'module' => 'work-schedules', 'label' => 'Manage All Work Schedules'],
            ['name' => 'manage-own-rotas-work-schedules', 'module' => 'work-schedules', 'label' => 'Manage Own Work Schedules'],
            ['name' => 'edit-rotas-work-schedules', 'module' => 'work-schedules', 'label' => 'Edit Work Schedules'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'Rotas',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            if ($company_role && !$company_role->hasPermissionTo($permission_obj)) {
                $company_role->givePermissionTo($permission_obj);
            }
        }
    }
}