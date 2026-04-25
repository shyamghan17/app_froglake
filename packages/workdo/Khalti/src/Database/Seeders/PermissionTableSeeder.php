<?php

namespace Workdo\Khalti\Database\Seeders;

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
        $module = 'khalti';

        $permission = [
            ['name' => 'khalti payment manage', 'module' => $module, 'label' => 'Manage Khalti Payment'],
        ];

        $company_role = Role::where('name', 'company')->first();
        $superadmin_role = Role::where('name', 'superadmin')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'Khalti',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            if ($company_role && !$company_role->hasPermissionTo($permission_obj)) {
                $company_role->givePermissionTo($permission_obj);
            }
            if ($superadmin_role && !$superadmin_role->hasPermissionTo($permission_obj)) {
                $superadmin_role->givePermissionTo($permission_obj);
            }
        }
    }
}
