<?php

namespace Workdo\Bookings\Database\Seeders;

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
            ['name' => 'manage-bookings-dashboard', 'module' => 'dashboard', 'label' => 'Manage Bookings Dashboard'],
            ['name' => 'manage-booking-settings', 'module' => 'settings', 'label' => 'Manage Booking Settings'],

            ['name' => 'manage-booking-brand-settings', 'module' => 'brand-settings', 'label' => 'Manage Booking Brand Settings'],
            ['name' => 'edit-booking-brand-settings', 'module' => 'brand-settings', 'label' => 'Edit Booking Brand Settings'],
            ['name' => 'manage-booking-banner-settings', 'module' => 'banner-settings', 'label' => 'Manage Booking Banner Settings'],
            ['name' => 'edit-booking-banner-settings', 'module' => 'banner-settings', 'label' => 'Edit Booking Banner Settings'],
            ['name' => 'manage-booking-appointment-settings', 'module' => 'appointment-settings', 'label' => 'Manage Booking Appointment Settings'],
            ['name' => 'edit-booking-appointment-settings', 'module' => 'appointment-settings', 'label' => 'Edit Booking Appointment Settings'],
            ['name' => 'manage-booking-additional-settings', 'module' => 'additional-settings', 'label' => 'Manage Booking Additional Settings'],
            ['name' => 'edit-booking-additional-settings', 'module' => 'additional-settings', 'label' => 'Edit Booking Additional Settings'],
            ['name' => 'manage-booking-contact-settings', 'module' => 'contact-settings', 'label' => 'Manage Booking Contact Settings'],
            ['name' => 'edit-booking-contact-settings', 'module' => 'contact-settings', 'label' => 'Edit Booking Contact Settings'],
            ['name' => 'manage-booking-about-us-settings', 'module' => 'about-us-settings', 'label' => 'Manage Booking About Us Settings'],
            ['name' => 'edit-booking-about-us-settings', 'module' => 'about-us-settings', 'label' => 'Edit Booking About Us Settings'],

            ['name' => 'manage-bookings', 'module' => 'bookings', 'label' => 'Manage Bookings'],
            ['name' => 'view-bookings', 'module' => 'bookings', 'label' => 'View Bookings'],
        
            ['name' => 'manage-booking-items', 'module' => 'items', 'label' => 'Manage Booking Items'],
            ['name' => 'manage-any-booking-items', 'module' => 'items', 'label' => 'Manage All Booking Items'],
            ['name' => 'manage-own-booking-items', 'module' => 'items', 'label' => 'Manage Own Booking Items'],
            ['name' => 'create-booking-items', 'module' => 'items', 'label' => 'Create Booking Items'],
            ['name' => 'edit-booking-items', 'module' => 'items', 'label' => 'Edit Booking Items'],
            ['name' => 'delete-booking-items', 'module' => 'items', 'label' => 'Delete Booking Items'],

            ['name' => 'manage-booking-staff', 'module' => 'staff', 'label' => 'Manage Booking Staff'],
            ['name' => 'manage-any-booking-staff', 'module' => 'staff', 'label' => 'Manage All Booking Staff'],
            ['name' => 'manage-own-booking-staff', 'module' => 'staff', 'label' => 'Manage Own Booking Staff'],
            ['name' => 'create-booking-staff', 'module' => 'staff', 'label' => 'Create Booking Staff'],
            ['name' => 'edit-booking-staff', 'module' => 'staff', 'label' => 'Edit Booking Staff'],
            ['name' => 'delete-booking-staff', 'module' => 'staff', 'label' => 'Delete Booking Staff'],

            ['name' => 'manage-booking-packages', 'module' => 'packages', 'label' => 'Manage Booking Packages'],
            ['name' => 'manage-any-booking-packages', 'module' => 'packages', 'label' => 'Manage All Booking Packages'],
            ['name' => 'manage-own-booking-packages', 'module' => 'packages', 'label' => 'Manage Own Booking Packages'],
            ['name' => 'create-booking-packages', 'module' => 'packages', 'label' => 'Create Booking Packages'],
            ['name' => 'edit-booking-packages', 'module' => 'packages', 'label' => 'Edit Booking Packages'],
            ['name' => 'delete-booking-packages', 'module' => 'packages', 'label' => 'Delete Booking Packages'],

            ['name' => 'manage-booking-customers', 'module' => 'customers', 'label' => 'Manage Booking Customers'],
            ['name' => 'manage-any-booking-customers', 'module' => 'customers', 'label' => 'Manage All Booking Customers'],
            ['name' => 'manage-own-booking-customers', 'module' => 'customers', 'label' => 'Manage Own Booking Customers'],
            ['name' => 'create-booking-customers', 'module' => 'customers', 'label' => 'Create Booking Customers'],
            ['name' => 'edit-booking-customers', 'module' => 'customers', 'label' => 'Edit Booking Customers'],
            ['name' => 'delete-booking-customers', 'module' => 'customers', 'label' => 'Delete Booking Customers'],

            ['name' => 'manage-booking-appointments', 'module' => 'appointments', 'label' => 'Manage Booking Appointments'],
            ['name' => 'manage-any-booking-appointments', 'module' => 'appointments', 'label' => 'Manage All Booking Appointments'],
            ['name' => 'manage-own-booking-appointments', 'module' => 'appointments', 'label' => 'Manage Own Booking Appointments'],
            ['name' => 'create-booking-appointments', 'module' => 'appointments', 'label' => 'Create Booking Appointments'],
            ['name' => 'edit-booking-appointments', 'module' => 'appointments', 'label' => 'Edit Booking Appointments'],
            ['name' => 'delete-booking-appointments', 'module' => 'appointments', 'label' => 'Delete Booking Appointments'],

            ['name' => 'manage-booking-custom-pages', 'module' => 'custom-pages', 'label' => 'Manage Booking Custom Pages'],
            ['name' => 'manage-any-booking-custom-pages', 'module' => 'custom-pages', 'label' => 'Manage All Booking Custom Pages'],
            ['name' => 'manage-own-booking-custom-pages', 'module' => 'custom-pages', 'label' => 'Manage Own Booking Custom Pages'],
            ['name' => 'create-booking-custom-pages', 'module' => 'custom-pages', 'label' => 'Create Booking Custom Pages'],
            ['name' => 'edit-booking-custom-pages', 'module' => 'custom-pages', 'label' => 'Edit Booking Custom Pages'],
            ['name' => 'delete-booking-custom-pages', 'module' => 'custom-pages', 'label' => 'Delete Booking Custom Pages'],

            ['name' => 'manage-booking-extra-services', 'module' => 'extra-services', 'label' => 'Manage Booking Extra Services'],
            ['name' => 'manage-any-booking-extra-services', 'module' => 'extra-services', 'label' => 'Manage All Booking Extra Services'],
            ['name' => 'manage-own-booking-extra-services', 'module' => 'extra-services', 'label' => 'Manage Own Booking Extra Services'],
            ['name' => 'create-booking-extra-services', 'module' => 'extra-services', 'label' => 'Create Booking Extra Services'],
            ['name' => 'edit-booking-extra-services', 'module' => 'extra-services', 'label' => 'Edit Booking Extra Services'],
            ['name' => 'delete-booking-extra-services', 'module' => 'extra-services', 'label' => 'Delete Booking Extra Services'],

            ['name' => 'manage-booking-reviews', 'module' => 'reviews', 'label' => 'Manage Booking Reviews'],
            ['name' => 'manage-any-booking-reviews', 'module' => 'reviews', 'label' => 'Manage All Booking Reviews'],
            ['name' => 'manage-own-booking-reviews', 'module' => 'reviews', 'label' => 'Manage Own Booking Reviews'],
            ['name' => 'create-booking-reviews', 'module' => 'reviews', 'label' => 'Create Booking Reviews'],
            ['name' => 'edit-booking-reviews', 'module' => 'reviews', 'label' => 'Edit Booking Reviews'],
            ['name' => 'delete-booking-reviews', 'module' => 'reviews', 'label' => 'Delete Booking Reviews'],

            ['name' => 'manage-booking-business-hours', 'module' => 'business-hours', 'label' => 'Manage Booking Business Hours'],
            ['name' => 'create-booking-business-hours', 'module' => 'business-hours', 'label' => 'Create Booking Business Hours'],
            ['name' => 'edit-booking-business-hours', 'module' => 'business-hours', 'label' => 'Edit Booking Business Hours'],
            ['name' => 'delete-booking-business-hours', 'module' => 'business-hours', 'label' => 'Delete Booking Business Hours'],

            ['name' => 'manage-booking-social-links', 'module' => 'social-links', 'label' => 'Manage Booking Social Links'],
            ['name' => 'manage-any-booking-social-links', 'module' => 'social-links', 'label' => 'Manage All Booking Social Links'],
            ['name' => 'manage-own-booking-social-links', 'module' => 'social-links', 'label' => 'Manage Own Booking Social Links'],
            ['name' => 'create-booking-social-links', 'module' => 'social-links', 'label' => 'Create Booking Social Links'],
            ['name' => 'edit-booking-social-links', 'module' => 'social-links', 'label' => 'Edit Booking Social Links'],
            ['name' => 'delete-booking-social-links', 'module' => 'social-links', 'label' => 'Delete Booking Social Links'],

            ['name' => 'manage-booking-contacts', 'module' => 'contacts', 'label' => 'Manage Booking Contacts'],
            ['name' => 'manage-any-booking-contacts', 'module' => 'contacts', 'label' => 'Manage All Booking Contacts'],
            ['name' => 'manage-own-booking-contacts', 'module' => 'contacts', 'label' => 'Manage Own Booking Contacts'],
            ['name' => 'delete-booking-contacts', 'module' => 'contacts', 'label' => 'Delete Booking Contacts'],

            ['name' => 'manage-booking-payments', 'module' => 'payments', 'label' => 'Manage Booking Payments'],
            ['name' => 'manage-any-booking-payments', 'module' => 'payments', 'label' => 'Manage All Booking Payments'],
            ['name' => 'manage-own-booking-payments', 'module' => 'payments', 'label' => 'Manage Own Booking Payments'],
            ['name' => 'view-booking-payments', 'module' => 'payments', 'label' => 'View Booking Payments'],

            // ExtraService management
            ['name' => 'manage-extra-services', 'module' => 'extra-services', 'label' => 'Manage ExtraServices'],
            ['name' => 'manage-any-extra-services', 'module' => 'extra-services', 'label' => 'Manage All ExtraServices'],
            ['name' => 'manage-own-extra-services', 'module' => 'extra-services', 'label' => 'Manage Own ExtraServices'],
            ['name' => 'create-extra-services', 'module' => 'extra-services', 'label' => 'Create ExtraServices'],
            ['name' => 'edit-extra-services', 'module' => 'extra-services', 'label' => 'Edit ExtraServices'],
            ['name' => 'delete-extra-services', 'module' => 'extra-services', 'label' => 'Delete ExtraServices'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'Bookings',
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