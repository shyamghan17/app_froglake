<?php

namespace Workdo\FindGoogleLeads\Database\Seeders;

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
            ['name' => 'manage-find-google-leads', 'module' => 'findgoogleleads', 'label' => 'Manage FindGoogleLeads'],
            ['name' => 'manage-any-find-google-leads', 'module' => 'findgoogleleads', 'label' => 'Manage All FindGoogleLeads'],
            ['name' => 'manage-own-find-google-leads', 'module' => 'findgoogleleads', 'label' => 'Manage Own FindGoogleLeads'],
            ['name' => 'create-find-google-leads', 'module' => 'findgoogleleads', 'label' => 'Create FindGoogleLeads'],
            ['name' => 'edit-find-google-leads', 'module' => 'findgoogleleads', 'label' => 'Edit FindGoogleLeads'],
            ['name' => 'view-find-google-leads', 'module' => 'findgoogleleads', 'label' => 'View FindGoogleLeads'],
            ['name' => 'delete-find-google-leads', 'module' => 'findgoogleleads', 'label' => 'Delete FindGoogleLeads'],
            ['name' => 'edit-findgoogleleads-settings', 'module' => 'findgoogleleads', 'label' => 'Edit FindGoogleLeads Settings'],
            ['name' => 'manage-findgoogleleads-settings', 'module' => 'findgoogleleads', 'label' => 'Manage FindGoogleLeads Settings'],
        ];


        $companyRole = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'FindGoogleLeads',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            if ($companyRole && !$companyRole->hasPermissionTo($permission_obj)) {
                $companyRole->givePermissionTo($permission_obj);
            }
        }
    }
}