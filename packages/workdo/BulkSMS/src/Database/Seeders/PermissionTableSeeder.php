<?php

namespace Workdo\BulkSMS\Database\Seeders;

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
            ['name' => 'manage-bulk-sms', 'module' => 'bulk-sms', 'label' => 'Manage Bulk SMS'],
            ['name' => 'edit-bulk-sms', 'module' => 'bulk-sms', 'label' => 'Edit Bulk SMS'],

            // BulkSmsContact management
            ['name' => 'manage-bulk-sms-contacts', 'module' => 'bulk-sms-contacts', 'label' => 'Manage Contacts'],
            ['name' => 'manage-any-bulk-sms-contacts', 'module' => 'bulk-sms-contacts', 'label' => 'Manage All Contacts'],
            ['name' => 'manage-own-bulk-sms-contacts', 'module' => 'bulk-sms-contacts', 'label' => 'Manage Own Contacts'],
            ['name' => 'create-bulk-sms-contacts', 'module' => 'bulk-sms-contacts', 'label' => 'Create Contacts'],
            ['name' => 'edit-bulk-sms-contacts', 'module' => 'bulk-sms-contacts', 'label' => 'Edit Contacts'],
            ['name' => 'delete-bulk-sms-contacts', 'module' => 'bulk-sms-contacts', 'label' => 'Delete Contacts'],

            // BulkSmsGroup management
            ['name' => 'manage-bulk-sms-groups', 'module' => 'bulk-sms-groups', 'label' => 'Manage Groups'],
            ['name' => 'manage-any-bulk-sms-groups', 'module' => 'bulk-sms-groups', 'label' => 'Manage All Groups'],
            ['name' => 'manage-own-bulk-sms-groups', 'module' => 'bulk-sms-groups', 'label' => 'Manage Own Groups'],
            ['name' => 'view-bulk-sms-groups', 'module' => 'bulk-sms-groups', 'label' => 'View Groups'],
            ['name' => 'create-bulk-sms-groups', 'module' => 'bulk-sms-groups', 'label' => 'Create Groups'],
            ['name' => 'edit-bulk-sms-groups', 'module' => 'bulk-sms-groups', 'label' => 'Edit Groups'],
            ['name' => 'delete-bulk-sms-groups', 'module' => 'bulk-sms-groups', 'label' => 'Delete Groups'],

            // SingleSms management
            ['name' => 'manage-single-sms', 'module' => 'single-sms', 'label' => 'Manage Single Sms'],
            ['name' => 'manage-any-single-sms', 'module' => 'single-sms', 'label' => 'Manage All Single Sms'],
            ['name' => 'manage-own-single-sms', 'module' => 'single-sms', 'label' => 'Manage Own Single Sms'],
            ['name' => 'view-single-sms', 'module' => 'single-sms', 'label' => 'View Single Sms'],
            ['name' => 'create-single-sms', 'module' => 'single-sms', 'label' => 'Create Single Sms'],
            ['name' => 'delete-single-sms', 'module' => 'single-sms', 'label' => 'Delete Single Sms'],

            // BulkSmsGroup Send management
            ['name' => 'manage-bulk-sms-groups-send', 'module' => 'bulk-sms-groups-send', 'label' => 'Manage Groups Send'],
            ['name' => 'manage-any-bulk-sms-groups-send', 'module' => 'bulk-sms-groups-send', 'label' => 'Manage All Groups Send'],
            ['name' => 'manage-own-bulk-sms-groups-send', 'module' => 'bulk-sms-groups-send', 'label' => 'Manage Own Groups Send'],
            ['name' => 'view-bulk-sms-groups-send', 'module' => 'bulk-sms-groups-send', 'label' => 'View Groups Send'],
            ['name' => 'create-bulk-sms-groups-send', 'module' => 'bulk-sms-groups-send', 'label' => 'Create Groups Send'],
            ['name' => 'delete-bulk-sms-groups-send', 'module' => 'bulk-sms-groups-send', 'label' => 'Delete Groups Send'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'BulkSMS',
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