<?php

namespace Workdo\SmartDashboardAnalytics\Database\Seeders;

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
            ['name' => 'manage-smart-dashboard', 'module' => 'smart-dashboard-analytics', 'label' => 'Manage Executive Overview Dashboard'],
            ['name' => 'manage-smart-financial', 'module' => 'smart-dashboard-analytics', 'label' => 'Manage Financial Analytics Dashboard'],
            // ['name' => 'manage-smart-team', 'module' => 'smart-dashboard-analytics', 'label' => 'Manage Team Performance Dashboard'],
            ['name' => 'manage-smart-sales', 'module' => 'smart-dashboard-analytics', 'label' => 'Manage Sales & Customer Dashboard'],
            ['name' => 'manage-smart-operations', 'module' => 'smart-dashboard-analytics', 'label' => 'Manage Operational Analytics Dashboard'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'SmartDashboardAnalytics',
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