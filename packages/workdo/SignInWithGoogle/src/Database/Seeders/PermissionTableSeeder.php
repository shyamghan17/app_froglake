<?php

namespace Workdo\SignInWithGoogle\Database\Seeders;

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
            ['name' => 'manage-google-signin-settings', 'module' => 'sign-in-with-google', 'label' => 'Manage Sign-In With Google Settings'],
            ['name' => 'edit-google-signin-settings', 'module' => 'sign-in-with-google', 'label' => 'Edit Sign-In With Google Settings'],
        ];

        $superadminRole = Role::where('name', 'superadmin')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'SignInWithGoogle',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            if ($superadminRole && !$superadminRole->hasPermissionTo($permission_obj)) {
                $superadminRole->givePermissionTo($permission_obj);
            }
        }
    }
}