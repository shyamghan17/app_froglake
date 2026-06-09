<?php

namespace Workdo\Portfolio\Database\Seeders;

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
            // mange portfolio
            ['name' => 'manage-portfolio', 'module' => 'portfolio', 'label' => 'Manage Portfolio'],

            // Category management
            ['name' => 'manage-portfolio-categories', 'module' => 'categories', 'label' => 'Manage Categories'],
            ['name' => 'manage-any-portfolio-categories', 'module' => 'categories', 'label' => 'Manage All Categories'],
            ['name' => 'manage-own-portfolio-categories', 'module' => 'categories', 'label' => 'Manage Own Categories'],
            ['name' => 'create-portfolio-categories', 'module' => 'categories', 'label' => 'Create Categories'],
            ['name' => 'edit-portfolio-categories', 'module' => 'categories', 'label' => 'Edit Categories'],
            ['name' => 'delete-portfolio-categories', 'module' => 'categories', 'label' => 'Delete Categories'],

            // Portfolio management
            ['name' => 'manage-portfolios', 'module' => 'portfolios', 'label' => 'Manage Portfolios'],
            ['name' => 'manage-any-portfolios', 'module' => 'portfolios', 'label' => 'Manage All Portfolios'],
            ['name' => 'manage-own-portfolios', 'module' => 'portfolios', 'label' => 'Manage Own Portfolios'],
            ['name' => 'copy-portfolios', 'module' => 'portfolios', 'label' => 'Copy Portfolios Link'],
            ['name' => 'create-portfolios', 'module' => 'portfolios', 'label' => 'Create Portfolios'],
            ['name' => 'edit-portfolios', 'module' => 'portfolios', 'label' => 'Edit Portfolios'],
            ['name' => 'delete-portfolios', 'module' => 'portfolios', 'label' => 'Delete Portfolios'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module'     => $perm['module'],
                    'label'      => $perm['label'],
                    'add_on'     => 'Portfolio',
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
