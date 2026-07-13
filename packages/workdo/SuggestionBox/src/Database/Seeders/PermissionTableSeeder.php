<?php

namespace Workdo\SuggestionBox\Database\Seeders;

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
            ['name' => 'manage-suggestion-box', 'module' => 'suggestion-box', 'label' => 'Manage SuggestionBox'],

            ['name' => 'manage-suggestion-categories', 'module' => 'suggestion-categories', 'label' => 'Manage Categories'],
            ['name' => 'manage-any-suggestion-categories', 'module' => 'suggestion-categories', 'label' => 'Manage All Categories'],
            ['name' => 'manage-own-suggestion-categories', 'module' => 'suggestion-categories', 'label' => 'Manage Own Categories'],
            ['name' => 'create-suggestion-categories', 'module' => 'suggestion-categories', 'label' => 'Create Categories'],
            ['name' => 'edit-suggestion-categories', 'module' => 'suggestion-categories', 'label' => 'Edit Categories'],
            ['name' => 'delete-suggestion-categories', 'module' => 'suggestion-categories', 'label' => 'Delete Categories'],

            ['name' => 'manage-suggestions', 'module' => 'suggestions', 'label' => 'Manage Suggestions'],
            ['name' => 'manage-any-suggestions', 'module' => 'suggestions', 'label' => 'Manage All Suggestions'],
            ['name' => 'manage-own-suggestions', 'module' => 'suggestions', 'label' => 'Manage Own Suggestions'],
            ['name' => 'view-suggestions', 'module' => 'suggestions', 'label' => 'View Suggestions'],
            ['name' => 'create-suggestions', 'module' => 'suggestions', 'label' => 'Create Suggestions'],
            ['name' => 'edit-suggestions', 'module' => 'suggestions', 'label' => 'Edit Suggestions'],
            ['name' => 'delete-suggestions', 'module' => 'suggestions', 'label' => 'Delete Suggestions'],
            ['name' => 'vote-suggestions', 'module' => 'suggestions', 'label' => 'Vote on Suggestions'],
            
            ['name' => 'manage-admin-dashboard', 'module' => 'admin', 'label' => 'Manage Admin Dashboard'],
            ['name' => 'respond-suggestions', 'module' => 'admin', 'label' => 'Respond to Suggestions'],

            ['name' => 'manage-suggestion-status-histories', 'module' => 'suggestion-status-histories', 'label' => 'Manage Status Histories'],
            ['name' => 'manage-any-suggestion-status-histories', 'module' => 'suggestion-status-histories', 'label' => 'Manage All Status Histories'],
            ['name' => 'manage-own-suggestion-status-histories', 'module' => 'suggestion-status-histories', 'label' => 'Manage Own Status Histories'],
            ['name' => 'view-suggestion-status-histories', 'module' => 'suggestion-status-histories', 'label' => 'View Status Histories'],
            ['name' => 'delete-suggestion-status-histories', 'module' => 'suggestion-status-histories', 'label' => 'Delete Status Histories'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module'     => $perm['module'],
                    'label'      => $perm['label'],
                    'add_on'     => 'SuggestionBox',
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
