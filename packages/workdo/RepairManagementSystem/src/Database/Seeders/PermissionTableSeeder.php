<?php

namespace Workdo\RepairManagementSystem\Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Workdo\RepairManagementSystem\Models\RepairOrderRequest;

class PermissionTableSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();
        Artisan::call('cache:clear');

        $permission = [
            ['name' => 'manage-repair-management-system', 'module' => 'repair-management-system', 'label' => 'Manage Repair Management'],
            
            // RepairOrderRequest management
            ['name' => 'manage-repair-order-requests', 'module' => 'repair-order-requests', 'label' => 'Order Requests'],
            ['name' => 'manage-any-repair-order-requests', 'module' => 'repair-order-requests', 'label' => 'Manage All Order Requests'],
            ['name' => 'manage-own-repair-order-requests', 'module' => 'repair-order-requests', 'label' => 'Manage Own Order Requests'],
            ['name' => 'view-repair-order-requests', 'module' => 'repair-order-requests', 'label' => 'View Order Requests'],
            ['name' => 'create-repair-order-requests', 'module' => 'repair-order-requests', 'label' => 'Create Order Requests'],
            ['name' => 'edit-repair-order-requests', 'module' => 'repair-order-requests', 'label' => 'Edit Order Requests'],
            ['name' => 'delete-repair-order-requests', 'module' => 'repair-order-requests', 'label' => 'Delete Order Requests'],
            ['name' => 'update-status-repair-order-requests', 'module' => 'repair-order-requests', 'label' => 'Update Order Requests Status'],
            ['name' => 'view-history-repair-order-requests', 'module' => 'repair-order-requests', 'label' => 'View Movement History'],
            ['name' => 'manage-repair-product-parts', 'module' => 'repair-order-requests', 'label' => 'Manage Product Parts'],

            // RepairInvoice management
            ['name' => 'manage-repair-invoices', 'module' => 'repair-invoices', 'label' => 'Manage Invoices'],
            ['name' => 'manage-any-repair-invoices', 'module' => 'repair-invoices', 'label' => 'Manage All Invoices'],
            ['name' => 'manage-own-repair-invoices', 'module' => 'repair-invoices', 'label' => 'Manage Own Invoices'],
            ['name' => 'view-repair-invoices', 'module' => 'repair-invoices', 'label' => 'View Invoices'],
            ['name' => 'create-repair-invoices', 'module' => 'repair-invoices', 'label' => 'Create Invoices'],
            ['name' => 'delete-repair-invoices', 'module' => 'repair-invoices', 'label' => 'Delete Invoices'],
            ['name' => 'make-payment-repair-invoices', 'module' => 'repair-invoices', 'label' => 'Make Payment'],
            ['name' => 'view-payment-history-repair-invoices', 'module' => 'repair-invoices', 'label' => 'View Payment History'],

            // RepairTechnician management
            ['name' => 'manage-repair-technicians', 'module' => 'repair-technicians', 'label' => 'Manage Technicians'],
            ['name' => 'manage-any-repair-technicians', 'module' => 'repair-technicians', 'label' => 'Manage All Technicians'],
            ['name' => 'manage-own-repair-technicians', 'module' => 'repair-technicians', 'label' => 'Manage Own Technicians'],
            ['name' => 'create-repair-technicians', 'module' => 'repair-technicians', 'label' => 'Create Technicians'],
            ['name' => 'edit-repair-technicians', 'module' => 'repair-technicians', 'label' => 'Edit Technicians'],
            ['name' => 'delete-repair-technicians', 'module' => 'repair-technicians', 'label' => 'Delete Technicians'],
            
            // RepairWarranty management
            ['name' => 'manage-repair-warranties', 'module' => 'repair-warranties', 'label' => 'Manage Warranties'],
            ['name' => 'manage-any-repair-warranties', 'module' => 'repair-warranties', 'label' => 'Manage All Warranties'],
            ['name' => 'manage-own-repair-warranties', 'module' => 'repair-warranties', 'label' => 'Manage Own Warranties'],
            ['name' => 'view-repair-warranties', 'module' => 'repair-warranties', 'label' => 'View Warranties'],
            ['name' => 'create-repair-warranties', 'module' => 'repair-warranties', 'label' => 'Create Warranties'],
            ['name' => 'edit-repair-warranties', 'module' => 'repair-warranties', 'label' => 'Edit Warranties'],
            ['name' => 'delete-repair-warranties', 'module' => 'repair-warranties', 'label' => 'Delete Warranties'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'RepairManagementSystem',
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