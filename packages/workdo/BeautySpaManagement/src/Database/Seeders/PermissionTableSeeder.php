<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

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
            ['name' => 'manage-beauty-spa-dashboard', 'module' => 'beauty-spa-dashboard', 'label' => 'Manage Beauty Spa Dashboard'],
            ['name' => 'manage-beauty-spa-management', 'module' => 'beauty-spa-management', 'label' => 'Manage Beauty SpaManagement'],

            // ServiceType management
            ['name' => 'manage-beauty-service-types', 'module' => 'beauty-service-types', 'label' => 'Manage Service Types'],
            ['name' => 'manage-any-beauty-service-types', 'module' => 'beauty-service-types', 'label' => 'Manage All Service Types'],
            ['name' => 'manage-own-beauty-service-types', 'module' => 'beauty-service-types', 'label' => 'Manage Own Service Types'],
            ['name' => 'create-beauty-service-types', 'module' => 'beauty-service-types', 'label' => 'Create Service Types'],
            ['name' => 'edit-beauty-service-types', 'module' => 'beauty-service-types', 'label' => 'Edit Service Types'],
            ['name' => 'delete-beauty-service-types', 'module' => 'beauty-service-types', 'label' => 'Delete Service Types'],

            // GiftCard management
            ['name' => 'manage-beauty-gift-cards', 'module' => 'beauty-gift-cards', 'label' => 'Manage Gift Cards'],
            ['name' => 'manage-any-beauty-gift-cards', 'module' => 'beauty-gift-cards', 'label' => 'Manage All Gift Cards'],
            ['name' => 'manage-own-beauty-gift-cards', 'module' => 'beauty-gift-cards', 'label' => 'Manage Own Gift Cards'],
            ['name' => 'create-beauty-gift-cards', 'module' => 'beauty-gift-cards', 'label' => 'Create Gift Cards'],
            ['name' => 'edit-beauty-gift-cards', 'module' => 'beauty-gift-cards', 'label' => 'Edit Gift Cards'],
            ['name' => 'delete-beauty-gift-cards', 'module' => 'beauty-gift-cards', 'label' => 'Delete Gift Cards'],

            // Working Hours management
            ['name' => 'manage-beauty-working', 'module' => 'beauty-working', 'label' => 'Manage Working Hour'],
            ['name' => 'edit-beauty-working', 'module' => 'beauty-working', 'label' => 'Edit Working Hour'],

            // Training management
            ['name' => 'manage-beauty-trainings', 'module' => 'beauty-trainings', 'label' => 'Manage Beauty Trainings'],
            ['name' => 'manage-any-beauty-trainings', 'module' => 'beauty-trainings', 'label' => 'Manage All Beauty Trainings'],
            ['name' => 'manage-own-beauty-trainings', 'module' => 'beauty-trainings', 'label' => 'Manage Own Beauty Trainings'],
            ['name' => 'create-beauty-trainings', 'module' => 'beauty-trainings', 'label' => 'Create Beauty Trainings'],
            ['name' => 'edit-beauty-trainings', 'module' => 'beauty-trainings', 'label' => 'Edit Beauty Trainings'],
            ['name' => 'delete-beauty-trainings', 'module' => 'beauty-trainings', 'label' => 'Delete Beauty Trainings'],

            // Certification management
            ['name' => 'manage-beauty-certifications', 'module' => 'beauty-certifications', 'label' => 'Manage Beauty Certifications'],
            ['name' => 'manage-any-beauty-certifications', 'module' => 'beauty-certifications', 'label' => 'Manage All Beauty Certifications'],
            ['name' => 'manage-own-beauty-certifications', 'module' => 'beauty-certifications', 'label' => 'Manage Own Beauty Certifications'],
            ['name' => 'create-beauty-certifications', 'module' => 'beauty-certifications', 'label' => 'Create Beauty Certifications'],
            ['name' => 'edit-beauty-certifications', 'module' => 'beauty-certifications', 'label' => 'Edit Beauty Certifications'],
            ['name' => 'delete-beauty-certifications', 'module' => 'beauty-certifications', 'label' => 'Delete Beauty Certifications'],

            // CustomPage management
            ['name' => 'manage-beauty-custom-pages', 'module' => 'beauty-custom-pages', 'label' => 'Manage Custom Pages'],
            ['name' => 'create-beauty-custom-pages', 'module' => 'beauty-custom-pages', 'label' => 'Create Custom Pages'],
            ['name' => 'edit-beauty-custom-pages', 'module' => 'beauty-custom-pages', 'label' => 'Edit Custom Pages'],
            ['name' => 'delete-beauty-custom-pages', 'module' => 'beauty-custom-pages', 'label' => 'Delete Custom Pages'],

            // Service management
            ['name' => 'manage-beauty-services', 'module' => 'beauty-services', 'label' => 'Manage Beauty Services'],
            ['name' => 'manage-any-beauty-services', 'module' => 'beauty-services', 'label' => 'Manage All Beauty Services'],
            ['name' => 'manage-own-beauty-services', 'module' => 'beauty-services', 'label' => 'Manage Own Beauty Services'],
            ['name' => 'create-beauty-services', 'module' => 'beauty-services', 'label' => 'Create Beauty Services'],
            ['name' => 'edit-beauty-services', 'module' => 'beauty-services', 'label' => 'Edit Beauty Services'],
            ['name' => 'view-beauty-services', 'module' => 'beauty-services', 'label' => 'View Beauty Services'],
            ['name' => 'delete-beauty-services', 'module' => 'beauty-services', 'label' => 'Delete Beauty Services'],

            // BeautyMembership management
            ['name' => 'manage-beauty-memberships', 'module' => 'beauty-memberships', 'label' => 'Manage Beauty Memberships'],
            ['name' => 'manage-any-beauty-memberships', 'module' => 'beauty-memberships', 'label' => 'Manage All Beauty Memberships'],
            ['name' => 'manage-own-beauty-memberships', 'module' => 'beauty-memberships', 'label' => 'Manage Own Beauty Memberships'],
            ['name' => 'view-beauty-memberships', 'module' => 'beauty-memberships', 'label' => 'View Beauty Memberships'],
            ['name' => 'create-beauty-memberships', 'module' => 'beauty-memberships', 'label' => 'Create Beauty Memberships'],
            ['name' => 'edit-beauty-memberships', 'module' => 'beauty-memberships', 'label' => 'Edit Beauty Memberships'],
            ['name' => 'delete-beauty-memberships', 'module' => 'beauty-memberships', 'label' => 'Delete Beauty Memberships'],

            // BeautyServiceOffer management
            ['name' => 'manage-beauty-service-offers', 'module' => 'beauty-service-offers', 'label' => 'Manage Beauty Servic Offers'],
            ['name' => 'manage-any-beauty-service-offers', 'module' => 'beauty-service-offers', 'label' => 'Manage All Beauty Servic Offers'],
            ['name' => 'manage-own-beauty-service-offers', 'module' => 'beauty-service-offers', 'label' => 'Manage Own Beauty Servic Offers'],
            ['name' => 'view-beauty-service-offers', 'module' => 'beauty-service-offers', 'label' => 'View Beauty Servic Offers'],
            ['name' => 'create-beauty-service-offers', 'module' => 'beauty-service-offers', 'label' => 'Create Beauty Servic Offers'],
            ['name' => 'edit-beauty-service-offers', 'module' => 'beauty-service-offers', 'label' => 'Edit Beauty Servic Offers'],
            ['name' => 'delete-beauty-service-offers', 'module' => 'beauty-service-offers', 'label' => 'Delete Beauty Servic Offers'],

            // BeautyLoyaltyProgram management
            ['name' => 'manage-beauty-loyalty-programs', 'module' => 'beauty-loyalty-programs', 'label' => 'Manage Beauty Loyalty Programs'],
            ['name' => 'manage-any-beauty-loyalty-programs', 'module' => 'beauty-loyalty-programs', 'label' => 'Manage All Beauty Loyalty Programs'],
            ['name' => 'manage-own-beauty-loyalty-programs', 'module' => 'beauty-loyalty-programs', 'label' => 'Manage Own Beauty Loyalty Programs'],
            ['name' => 'create-beauty-loyalty-programs', 'module' => 'beauty-loyalty-programs', 'label' => 'Create Beauty Loyalty Programs'],
            ['name' => 'edit-beauty-loyalty-programs', 'module' => 'beauty-loyalty-programs', 'label' => 'Edit Beauty Loyalty Programs'],
            ['name' => 'delete-beauty-loyalty-programs', 'module' => 'beauty-loyalty-programs', 'label' => 'Delete Beauty Loyalty Programs'],

            // Brand Settings management
            ['name' => 'manage-beauty-brand-settings', 'module' => 'beauty-brand-settings', 'label' => 'Manage Brand Settings'],
            ['name' => 'edit-beauty-brand-settings', 'module' => 'beauty-brand-settings', 'label' => 'Edit Brand Settings'],

            // Banner Section management
            ['name' => 'manage-beauty-banner-section', 'module' => 'beauty-banner-section', 'label' => 'Manage Banner Section'],
            ['name' => 'edit-beauty-banner-section', 'module' => 'beauty-banner-section', 'label' => 'Edit Banner Section'],

            // Feature Section management
            ['name' => 'manage-beauty-feature-section', 'module' => 'beauty-feature-section', 'label' => 'Manage Feature Section'],
            ['name' => 'edit-beauty-feature-section', 'module' => 'beauty-feature-section', 'label' => 'Edit Feature Section'],

            // Testimonials management
            ['name' => 'manage-beauty-testimonials', 'module' => 'beauty-testimonials', 'label' => 'Manage Testimonials'],
            ['name' => 'edit-beauty-testimonials', 'module' => 'beauty-testimonials', 'label' => 'Edit Testimonials'],

            // About Section management
            ['name' => 'manage-beauty-about-section', 'module' => 'beauty-about-section', 'label' => 'Manage About Section'],
            ['name' => 'edit-beauty-about-section', 'module' => 'beauty-about-section', 'label' => 'Edit About Section'],

            // Contact Info management
            ['name' => 'manage-beauty-contact-info', 'module' => 'beauty-contact-info', 'label' => 'Manage Contact Info'],
            ['name' => 'edit-beauty-contact-info', 'module' => 'beauty-contact-info', 'label' => 'Edit Contact Info'],

            // Social Links management
            ['name' => 'manage-beauty-social-links', 'module' => 'beauty-social-links', 'label' => 'Manage Social Links'],
            ['name' => 'edit-beauty-social-links', 'module' => 'beauty-social-links', 'label' => 'Edit Social Links'],

            // BeautyBooking management
            ['name' => 'manage-beauty-bookings', 'module' => 'beauty-bookings', 'label' => 'Manage Beauty Bookings'],
            ['name' => 'manage-any-beauty-bookings', 'module' => 'beauty-bookings', 'label' => 'Manage All Beauty Bookings'],
            ['name' => 'manage-own-beauty-bookings', 'module' => 'beauty-bookings', 'label' => 'Manage Own Beauty Bookings'],
            ['name' => 'create-beauty-bookings', 'module' => 'beauty-bookings', 'label' => 'Create Beauty Bookings'],
            ['name' => 'view-beauty-bookings', 'module' => 'beauty-bookings', 'label' => 'View Beauty Bookings'],
            ['name' => 'edit-beauty-bookings', 'module' => 'beauty-bookings', 'label' => 'Edit Beauty Bookings'],
            ['name' => 'delete-beauty-bookings', 'module' => 'beauty-bookings', 'label' => 'Delete Beauty Bookings'],
            ['name' => 'manage-beauty-bookings-payment', 'module' => 'beauty-bookings', 'label' => 'Mark Beauty Booking Payments'],
            ['name' => 'delete-beauty-bookings-payment', 'module' => 'beauty-bookings', 'label' => 'Delete Beauty Booking Payments'],
            ['name' => 'beauty-bookings-payments-paid', 'module' => 'beauty-bookings', 'label' => 'Mark Beauty Booking Payments as Paid'],

            // Beauty Receipt management
            ['name' => 'manage-beauty-receipt', 'module' => 'beauty-receipt', 'label' => 'Manage Beauty Receipt'],
            ['name' => 'manage-any-beauty-receipt', 'module' => 'beauty-receipt', 'label' => 'Manage All Beauty Receipt'],
            ['name' => 'manage-own-beauty-receipt', 'module' => 'beauty-receipt', 'label' => 'Manage Own Beauty Receipt'],
            ['name' => 'view-beauty-receipt', 'module' => 'beauty-receipt', 'label' => 'View Beauty Receipt'],
            ['name' => 'download-beauty-receipt', 'module' => 'beauty-receipt', 'label' => 'Download Beauty Receipt'],

            // Beauty Subscribers
            ['name' => 'manage-beauty-subscribers', 'module' => 'beauty-subscribers', 'label' => 'Manage Beauty Subscribers'],
            ['name' => 'delete-beauty-subscribers', 'module' => 'beauty-subscribers', 'label' => 'Delete Beauty Subscribers'],
            // Beauty Contacts
            ['name' => 'manage-beauty-contacts', 'module' => 'beauty-contacts', 'label' => 'Manage Beauty Contacts'],
            ['name' => 'delete-beauty-contacts', 'module' => 'beauty-contacts', 'label' => 'Delete Beauty Contacts'],
            // Beauty review
            ['name' => 'manage-beauty-reviews', 'module' => 'beauty-reviews', 'label' => 'Manage Beauty Reviews'],
            ['name' => 'delete-beauty-reviews', 'module' => 'beauty-reviews', 'label' => 'Delete Beauty Reviews'],
       
            // Beauty Home Section
            ['name' => 'manage-beauty-home-section', 'module' => 'beauty-home-section', 'label' => 'Manage Home Section'],
            ['name' => 'edit-beauty-home-section', 'module' => 'beauty-home-section', 'label' => 'Edit Home Section'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'BeautySpaManagement',
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