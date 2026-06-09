<?php

namespace Workdo\EBilling\Database\Seeders;

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
            ['name' => 'manage-ebilling', 'module' => 'ebilling', 'label' => 'Manage EBilling'],
            ['name' => 'manage-any-ebilling', 'module' => 'ebilling', 'label' => 'Manage All EBilling'],
            ['name' => 'manage-own-ebilling', 'module' => 'ebilling', 'label' => 'Manage Own EBilling'],
            ['name' => 'view-ebilling', 'module' => 'ebilling', 'label' => 'View EBilling'],
            ['name' => 'create-ebilling', 'module' => 'ebilling', 'label' => 'Create EBilling'],
            ['name' => 'edit-ebilling', 'module' => 'ebilling', 'label' => 'Edit EBilling'],
            ['name' => 'delete-ebilling', 'module' => 'ebilling', 'label' => 'Delete EBilling'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'EBilling',
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