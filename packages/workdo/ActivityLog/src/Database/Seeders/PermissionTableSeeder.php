<?php

namespace Workdo\ActivityLog\Database\Seeders;

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
            ['name' => 'manage-activity-log', 'module' => 'activity log', 'label' => 'Manage Activity Log'],
            ['name' => 'manage-any-activity-log', 'module' => 'activity log', 'label' => 'Manage All Activity Log'],
            ['name' => 'manage-own-activity-log', 'module' => 'activity log', 'label' => 'Manage Own Activity Log'],                   
            ['name' => 'delete-activity-log', 'module' => 'activity log', 'label' => 'Delete Activity Log'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'ActivityLog',
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