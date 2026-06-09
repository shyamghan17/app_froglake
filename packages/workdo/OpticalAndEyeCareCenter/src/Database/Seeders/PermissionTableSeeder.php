<?php

namespace Workdo\OpticalAndEyeCareCenter\Database\Seeders;

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
            ['name' => 'manage-optical-and-eye-care-center', 'module' => 'optical-and-eye-care-center', 'label' => 'Manage OpticalAndEyeCareCenter'],
            ['name' => 'manage-optical-dashboard', 'module' => 'optical-and-eye-care-center', 'label' => 'Manage Optical Dashboard'],

            ['name' => 'manage-optical-doctors', 'module' => 'optical-and-eye-care-center-doctor', 'label' => 'Manage Optical Doctors'],
            ['name' => 'manage-any-optical-doctors', 'module' => 'optical-and-eye-care-center-doctor', 'label' => 'Manage All Optical Doctors'],
            ['name' => 'manage-own-optical-doctors', 'module' => 'optical-and-eye-care-center-doctor', 'label' => 'Manage Own Optical Doctors'],
            ['name' => 'view-optical-doctors', 'module' => 'optical-and-eye-care-center-doctor', 'label' => 'View Optical Doctors'],
            ['name' => 'create-optical-doctors', 'module' => 'optical-and-eye-care-center-doctor', 'label' => 'Create Optical Doctors'],
            ['name' => 'edit-optical-doctors', 'module' => 'optical-and-eye-care-center-doctor', 'label' => 'Edit Optical Doctors'],
            ['name' => 'delete-optical-doctors', 'module' => 'optical-and-eye-care-center-doctor', 'label' => 'Delete Optical Doctors'],

            // EyePatient management
            ['name' => 'manage-eye-patients', 'module' => 'eye-patients', 'label' => 'Manage EyePatients'],
            ['name' => 'manage-any-eye-patients', 'module' => 'eye-patients', 'label' => 'Manage All EyePatients'],
            ['name' => 'manage-own-eye-patients', 'module' => 'eye-patients', 'label' => 'Manage Own EyePatients'],
            ['name' => 'view-eye-patients', 'module' => 'eye-patients', 'label' => 'View EyePatients'],
            ['name' => 'create-eye-patients', 'module' => 'eye-patients', 'label' => 'Create EyePatients'],
            ['name' => 'edit-eye-patients', 'module' => 'eye-patients', 'label' => 'Edit EyePatients'],
            ['name' => 'delete-eye-patients', 'module' => 'eye-patients', 'label' => 'Delete EyePatients'],

            // EyeTestPrescription management
            ['name' => 'manage-eye-test-prescriptions', 'module' => 'eye-test-prescriptions', 'label' => 'Manage EyeTestPrescriptions'],
            ['name' => 'manage-any-eye-test-prescriptions', 'module' => 'eye-test-prescriptions', 'label' => 'Manage All EyeTestPrescriptions'],
            ['name' => 'manage-own-eye-test-prescriptions', 'module' => 'eye-test-prescriptions', 'label' => 'Manage Own EyeTestPrescriptions'],
            ['name' => 'view-eye-test-prescriptions', 'module' => 'eye-test-prescriptions', 'label' => 'View EyeTestPrescriptions'],
            ['name' => 'create-eye-test-prescriptions', 'module' => 'eye-test-prescriptions', 'label' => 'Create EyeTestPrescriptions'],
            ['name' => 'edit-eye-test-prescriptions', 'module' => 'eye-test-prescriptions', 'label' => 'Edit EyeTestPrescriptions'],
            ['name' => 'delete-eye-test-prescriptions', 'module' => 'eye-test-prescriptions', 'label' => 'Delete EyeTestPrescriptions'],

            // EyeCareAppoinment management
            ['name' => 'manage-eye-care-appoinments', 'module' => 'eye-care-appoinments', 'label' => 'Manage EyeCareAppointments'],
            ['name' => 'manage-any-eye-care-appoinments', 'module' => 'eye-care-appoinments', 'label' => 'Manage All EyeCareAppointments'],
            ['name' => 'manage-own-eye-care-appoinments', 'module' => 'eye-care-appoinments', 'label' => 'Manage Own EyeCareAppointments'],
            ['name' => 'view-eye-care-appoinments', 'module' => 'eye-care-appoinments', 'label' => 'View EyeCareAppointments'],
            ['name' => 'create-eye-care-appoinments', 'module' => 'eye-care-appoinments', 'label' => 'Create EyeCareAppointments'],
            ['name' => 'edit-eye-care-appoinments', 'module' => 'eye-care-appoinments', 'label' => 'Edit EyeCareAppointments'],
            ['name' => 'delete-eye-care-appoinments', 'module' => 'eye-care-appoinments', 'label' => 'Delete EyeCareAppointments'],

            // EyewearItem management
            ['name' => 'manage-eyewear-items', 'module' => 'eyewear-items', 'label' => 'Manage EyewearItems'],
            ['name' => 'manage-any-eyewear-items', 'module' => 'eyewear-items', 'label' => 'Manage All EyewearItems'],
            ['name' => 'manage-own-eyewear-items', 'module' => 'eyewear-items', 'label' => 'Manage Own EyewearItems'],
            ['name' => 'view-eyewear-items', 'module' => 'eyewear-items', 'label' => 'View EyewearItems'],
            ['name' => 'create-eyewear-items', 'module' => 'eyewear-items', 'label' => 'Create EyewearItems'],
            ['name' => 'edit-eyewear-items', 'module' => 'eyewear-items', 'label' => 'Edit EyewearItems'],
            ['name' => 'delete-eyewear-items', 'module' => 'eyewear-items', 'label' => 'Delete EyewearItems'],

            // EyewearOrder management
            ['name' => 'manage-eyewear-orders', 'module' => 'eyewear-orders', 'label' => 'Manage EyewearOrders'],
            ['name' => 'manage-any-eyewear-orders', 'module' => 'eyewear-orders', 'label' => 'Manage All EyewearOrders'],
            ['name' => 'manage-own-eyewear-orders', 'module' => 'eyewear-orders', 'label' => 'Manage Own EyewearOrders'],
            ['name' => 'view-eyewear-orders', 'module' => 'eyewear-orders', 'label' => 'View EyewearOrders'],
            ['name' => 'post-eyewear-orders', 'module' => 'eyewear-orders', 'label' => 'Post EyewearOrders'],
            ['name' => 'print-eyewear-orders', 'module' => 'eyewear-orders', 'label' => 'Print EyewearOrders'],
            ['name' => 'create-eyewear-orders', 'module' => 'eyewear-orders', 'label' => 'Create EyewearOrders'],
            ['name' => 'edit-eyewear-orders', 'module' => 'eyewear-orders', 'label' => 'Edit EyewearOrders'],
            ['name' => 'delete-eyewear-orders', 'module' => 'eyewear-orders', 'label' => 'Delete EyewearOrders'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'OpticalAndEyeCareCenter',
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
