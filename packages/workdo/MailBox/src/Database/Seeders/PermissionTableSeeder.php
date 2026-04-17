<?php

namespace Workdo\MailBox\Database\Seeders;

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
            // MailBox Email Management
            ['name' => 'manage-mailbox', 'module' => 'mailbox', 'label' => 'Manage MailBox'],
            ['name' => 'view-mailbox-email', 'module' => 'mailbox', 'label' => 'View Email'],
            ['name' => 'create-email-mailbox', 'module' => 'mailbox', 'label' => 'Compose/Send Email'],
            ['name' => 'reply-email-mailbox', 'module' => 'mailbox', 'label' => 'Reply Email'],
            ['name' => 'delete-email-mailbox', 'module' => 'mailbox', 'label' => 'Delete Email'],
            ['name' => 'action-email-mailbox', 'module' => 'mailbox', 'label' => 'Email Page Actions'],

            // MailBox Settings (Separate Module)
            ['name' => 'manage-mailbox-settings', 'module' => 'mailbox-settings', 'label' => 'Manage MailBox Settings'],
            ['name' => 'create-mailbox-settings', 'module' => 'mailbox-settings', 'label' => 'Create Email Account'],
            ['name' => 'switch-mailbox-settings', 'module' => 'mailbox-settings', 'label' => 'Switch Email Account'],
            ['name' => 'delete-mailbox-settings', 'module' => 'mailbox-settings', 'label' => 'Delete Email Account'],
            ['name' => 'test-mailbox-connection', 'module' => 'mailbox-settings', 'label' => 'Test Email Connection'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'MailBox',
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
