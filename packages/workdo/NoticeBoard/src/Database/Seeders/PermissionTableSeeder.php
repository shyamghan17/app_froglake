<?php

namespace Workdo\NoticeBoard\Database\Seeders;

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
            ['name' => 'manage-notice-board', 'module' => 'notice-board', 'label' => 'Manage Notice Board'],

            // Notice management
            ['name' => 'manage-notices', 'module' => 'notices', 'label' => 'Manage Notices'],
            ['name' => 'manage-any-notices', 'module' => 'notices', 'label' => 'Manage All Notices'],
            ['name' => 'manage-own-notices', 'module' => 'notices', 'label' => 'Manage Own Notices'],
            ['name' => 'view-notices', 'module' => 'notices', 'label' => 'View Notices'],
            ['name' => 'create-notices', 'module' => 'notices', 'label' => 'Create Notices'],
            ['name' => 'edit-notices', 'module' => 'notices', 'label' => 'Edit Notices'],
            ['name' => 'delete-notices', 'module' => 'notices', 'label' => 'Delete Notices'],
            ['name' => 'pin-unpin-notices', 'module' => 'notices', 'label' => 'Pin/Unpin Notices'],
            ['name' => 'manage-notice-status', 'module' => 'notices', 'label' => 'Manage Notice Status'],
            ['name' => 'read-stats-notices', 'module' => 'notices', 'label' => 'View Notice Read Statistics'],

            // Notice comments
            ['name' => 'manage-any-notices-comments', 'module' => 'notices-comment', 'label' => 'Manage All Notice Comments'],
            ['name' => 'reply-notices-comments', 'module' => 'notices-comment', 'label' => 'Reply to Notice Comments'],
            ['name' => 'delete-any-notices-comments', 'module' => 'notices-comment', 'label' => 'Delete Any Notice Comment'],
            ['name' => 'delete-own-notices-comments', 'module' => 'notices-comment', 'label' => 'Delete Own Notice Comment'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module'     => $perm['module'],
                    'label'      => $perm['label'],
                    'add_on'     => 'NoticeBoard',
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
