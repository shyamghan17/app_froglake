<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

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
            ['name' => 'manage-photo-studio-management', 'module' => 'photo-studio-management', 'label' => 'Manage Photo Studio Management'],
            ['name' => 'manage-photo-studio-management-dashboard', 'module' => 'photo-studio-management', 'label' => 'Manage Dashboard'],

            // System Setup - Brand Settings
            ['name' => 'manage-photo-studio-brand-settings', 'module' => 'system-setup', 'label' => 'Manage Brand Settings'],
            ['name' => 'edit-photo-studio-brand-settings', 'module' => 'system-setup', 'label' => 'Edit Brand Settings'],

            // System Setup - Banner Section
            ['name' => 'manage-photo-studio-banner-section', 'module' => 'system-setup', 'label' => 'Manage Banner Section'],
            ['name' => 'edit-photo-studio-banner-section', 'module' => 'system-setup', 'label' => 'Edit Banner Section'],

            // System Setup - About Section
            ['name' => 'manage-photo-studio-about-section', 'module' => 'system-setup', 'label' => 'Manage About Section'],
            ['name' => 'edit-photo-studio-about-section', 'module' => 'system-setup', 'label' => 'Edit About Section'],

            // System Setup - Title Section
            ['name' => 'manage-photo-studio-title-section', 'module' => 'system-setup', 'label' => 'Manage Title Section'],
            ['name' => 'edit-photo-studio-title-section', 'module' => 'system-setup', 'label' => 'Edit Title Section'],

            // System Setup - Testimonials
            ['name' => 'manage-photo-studio-testimonials', 'module' => 'system-setup', 'label' => 'Manage Testimonials'],
            ['name' => 'edit-photo-studio-testimonials', 'module' => 'system-setup', 'label' => 'Edit Testimonials'],

            // System Setup - Gallery Section
            ['name' => 'manage-photo-studio-gallery-section', 'module' => 'system-setup', 'label' => 'Manage Gallery Section'],
            ['name' => 'edit-photo-studio-gallery-section', 'module' => 'system-setup', 'label' => 'Edit Gallery Section'],
        
              // System Setup - Award Section
            ['name' => 'manage-photo-studio-award-section', 'module' => 'system-setup', 'label' => 'Manage Award Section'],
            ['name' => 'edit-photo-studio-award-section', 'module' => 'system-setup', 'label' => 'Edit Award Section'],

            // System Setup - Media Section
            ['name' => 'manage-photo-studio-media-section', 'module' => 'system-setup', 'label' => 'Manage Media Section'],
            ['name' => 'edit-photo-studio-media-section', 'module' => 'system-setup', 'label' => 'Edit Media Section'],

            // System Setup - FAQ
            ['name' => 'manage-photo-studio-faqs', 'module' => 'system-setup', 'label' => 'Manage FAQs'],
            ['name' => 'edit-photo-studio-faqs', 'module' => 'system-setup', 'label' => 'Edit FAQs'],

            // System Setup - Contact Section
            ['name' => 'manage-photo-studio-contact-section', 'module' => 'system-setup', 'label' => 'Manage Contact Section'],
            ['name' => 'edit-photo-studio-contact-section', 'module' => 'system-setup', 'label' => 'Edit Contact Section'],

            // System Setup - Footer Section
            ['name' => 'manage-photo-studio-footer-section', 'module' => 'system-setup', 'label' => 'Manage Footer Section'],
            ['name' => 'edit-photo-studio-footer-section', 'module' => 'system-setup', 'label' => 'Edit Footer Section'],

            // System Setup - Gallery Types
            ['name' => 'manage-photo-studio-gallery-type', 'module' => 'gallery-types', 'label' => 'Manage Gallery Types'],
            ['name' => 'manage-any-photo-studio-gallery-type', 'module' => 'gallery-types', 'label' => 'Manage Any Gallery Type'],
            ['name' => 'manage-own-photo-studio-gallery-type', 'module' => 'gallery-types', 'label' => 'Manage Own Gallery Type'],
            ['name' => 'create-photo-studio-gallery-type', 'module' => 'gallery-types', 'label' => 'Create Gallery Type'],
            ['name' => 'edit-photo-studio-gallery-type', 'module' => 'gallery-types', 'label' => 'Edit Gallery Type'],
            ['name' => 'delete-photo-studio-gallery-type', 'module' => 'gallery-types', 'label' => 'Delete Gallery Type'],

            // System Setup - Equipment Tags
            ['name' => 'manage-photo-studio-equipment-tag', 'module' => 'equipment-tags', 'label' => 'Manage Equipment Tags'],
            ['name' => 'manage-any-photo-studio-equipment-tag', 'module' => 'equipment-tags', 'label' => 'Manage Any Equipment Tag'],
            ['name' => 'manage-own-photo-studio-equipment-tag', 'module' => 'equipment-tags', 'label' => 'Manage Own Equipment Tag'],
            ['name' => 'create-photo-studio-equipment-tag', 'module' => 'equipment-tags', 'label' => 'Create Equipment Tag'],
            ['name' => 'edit-photo-studio-equipment-tag', 'module' => 'equipment-tags', 'label' => 'Edit Equipment Tag'],
            ['name' => 'delete-photo-studio-equipment-tag', 'module' => 'equipment-tags', 'label' => 'Delete Equipment Tag'],

            // System Setup - Equipment Types
            ['name' => 'manage-photo-studio-equipment-type', 'module' => 'equipment-types', 'label' => 'Manage Equipment Types'],
            ['name' => 'manage-any-photo-studio-equipment-type', 'module' => 'equipment-types', 'label' => 'Manage Any Equipment Type'],
            ['name' => 'manage-own-photo-studio-equipment-type', 'module' => 'equipment-types', 'label' => 'Manage Own Equipment Type'],
            ['name' => 'create-photo-studio-equipment-type', 'module' => 'equipment-types', 'label' => 'Create Equipment Type'],
            ['name' => 'edit-photo-studio-equipment-type', 'module' => 'equipment-types', 'label' => 'Edit Equipment Type'],
            ['name' => 'delete-photo-studio-equipment-type', 'module' => 'equipment-types', 'label' => 'Delete Equipment Type'],

          

            // System Setup - Service Categories
            ['name' => 'manage-photo-studio-service-category', 'module' => 'service-categories', 'label' => 'Manage Service Categories'],
            ['name' => 'manage-any-photo-studio-service-category', 'module' => 'service-categories', 'label' => 'Manage Any Service Category'],
            ['name' => 'manage-own-photo-studio-service-category', 'module' => 'service-categories', 'label' => 'Manage Own Service Category'],
            ['name' => 'create-photo-studio-service-category', 'module' => 'service-categories', 'label' => 'Create Service Category'],
            ['name' => 'edit-photo-studio-service-category', 'module' => 'service-categories', 'label' => 'Edit Service Category'],
            ['name' => 'delete-photo-studio-service-category', 'module' => 'service-categories', 'label' => 'Delete Service Category'],

            // System Setup - Custom Pages
            ['name' => 'manage-photo-studio-custom-pages', 'module' => 'custom-pages', 'label' => 'Manage Custom Pages'],
            ['name' => 'manage-any-photo-studio-custom-pages', 'module' => 'custom-pages', 'label' => 'Manage Any Custom Page'],
            ['name' => 'manage-own-photo-studio-custom-pages', 'module' => 'custom-pages', 'label' => 'Manage Own Custom Page'],
            ['name' => 'create-photo-studio-custom-pages', 'module' => 'custom-pages', 'label' => 'Create Custom Pages'],
            ['name' => 'edit-photo-studio-custom-pages', 'module' => 'custom-pages', 'label' => 'Edit Custom Pages'],
            ['name' => 'delete-photo-studio-custom-pages', 'module' => 'custom-pages', 'label' => 'Delete Custom Pages'],

            // Camera Kits
            ['name' => 'manage-photo-studio-camera-kit', 'module' => 'camera-kits', 'label' => 'Manage Camera Kits'],
            ['name' => 'manage-any-photo-studio-camera-kit', 'module' => 'camera-kits', 'label' => 'Manage Any Camera Kit'],
            ['name' => 'manage-own-photo-studio-camera-kit', 'module' => 'camera-kits', 'label' => 'Manage Own Camera Kit'],
            ['name' => 'view-photo-studio-camera-kit', 'module' => 'camera-kits', 'label' => 'View Camera Kit'],
            ['name' => 'create-photo-studio-camera-kit', 'module' => 'camera-kits', 'label' => 'Create Camera Kit'],
            ['name' => 'edit-photo-studio-camera-kit', 'module' => 'camera-kits', 'label' => 'Edit Camera Kit'],
            ['name' => 'delete-photo-studio-camera-kit', 'module' => 'camera-kits', 'label' => 'Delete Camera Kit'],

            // Team Members
            ['name' => 'manage-photo-studio-team-members', 'module' => 'team-members', 'label' => 'Manage Team Members'],
            ['name' => 'manage-any-photo-studio-team-members', 'module' => 'team-members', 'label' => 'Manage Any Team Member'],
            ['name' => 'manage-own-photo-studio-team-members', 'module' => 'team-members', 'label' => 'Manage Own Team Member'],
            ['name' => 'create-photo-studio-team-members', 'module' => 'team-members', 'label' => 'Create Team Member'],
            ['name' => 'edit-photo-studio-team-members', 'module' => 'team-members', 'label' => 'Edit Team Member'],
            ['name' => 'delete-photo-studio-team-members', 'module' => 'team-members', 'label' => 'Delete Team Member'],
            ['name' => 'view-photo-studio-team-members', 'module' => 'team-members', 'label' => 'View Team Member'],

            // Services
            ['name' => 'manage-photo-studio-service', 'module' => 'studio-services', 'label' => 'Manage Services'],
            ['name' => 'manage-any-photo-studio-service', 'module' => 'studio-services', 'label' => 'Manage Any Service'],
            ['name' => 'manage-own-photo-studio-service', 'module' => 'studio-services', 'label' => 'Manage Own Service'],
            ['name' => 'view-photo-studio-service', 'module' => 'studio-services', 'label' => 'View Service'],
            ['name' => 'create-photo-studio-service', 'module' => 'studio-services', 'label' => 'Create Service'],
            ['name' => 'edit-photo-studio-service', 'module' => 'studio-services', 'label' => 'Edit Service'],
            ['name' => 'delete-photo-studio-service', 'module' => 'studio-services', 'label' => 'Delete Service'],

            // Contacts
            ['name' => 'manage-photo-studio-contacts', 'module' => 'studio-contacts', 'label' => 'Manage Contacts'],
            ['name' => 'manage-any-photo-studio-contacts', 'module' => 'studio-contacts', 'label' => 'Manage Any Contact'],
            ['name' => 'manage-own-photo-studio-contacts', 'module' => 'studio-contacts', 'label' => 'Manage Own Contact'],
            ['name' => 'view-photo-studio-contacts', 'module' => 'studio-contacts', 'label' => 'View Contact'],
            ['name' => 'delete-photo-studio-contacts', 'module' => 'studio-contacts', 'label' => 'Delete Contact'],

            // Subscribers
            ['name' => 'manage-photo-studio-subscribers', 'module' => 'studio-subscribers', 'label' => 'Manage Subscribers'],
            ['name' => 'manage-any-photo-studio-subscribers', 'module' => 'studio-subscribers', 'label' => 'Manage Any Subscriber'],
            ['name' => 'manage-own-photo-studio-subscribers', 'module' => 'studio-subscribers', 'label' => 'Manage Own Subscriber'],
            ['name' => 'view-photo-studio-subscribers', 'module' => 'studio-subscribers', 'label' => 'View Subscriber'],
            ['name' => 'delete-photo-studio-subscribers', 'module' => 'studio-subscribers', 'label' => 'Delete Subscriber'],

            // Appointments
            ['name' => 'manage-photo-studio-appointments', 'module' => 'studio-appointments', 'label' => 'Manage Appointments'],
            ['name' => 'manage-any-photo-studio-appointments', 'module' => 'studio-appointments', 'label' => 'Manage Any Appointment'],
            ['name' => 'manage-own-photo-studio-appointments', 'module' => 'studio-appointments', 'label' => 'Manage Own Appointment'],
            ['name' => 'view-photo-studio-appointments', 'module' => 'studio-appointments', 'label' => 'View Appointment'],
            ['name' => 'create-photo-studio-appointments', 'module' => 'studio-appointments', 'label' => 'Create Appointment'],
            ['name' => 'edit-photo-studio-appointments', 'module' => 'studio-appointments', 'label' => 'Edit Appointment'],
            ['name' => 'delete-photo-studio-appointments', 'module' => 'studio-appointments', 'label' => 'Delete Appointment'],

            // Appointment Payments
            ['name' => 'manage-photo-studio-appointment-payments', 'module' => 'appointment-payments', 'label' => 'Manage Appointment Payments'],
            ['name' => 'manage-any-photo-studio-appointment-payments', 'module' => 'appointment-payments', 'label' => 'Manage Any Appointment Payment'],
            ['name' => 'manage-own-photo-studio-appointment-payments', 'module' => 'appointment-payments', 'label' => 'Manage Own Appointment Payment'],
            ['name' => 'view-photo-studio-appointment-payments', 'module' => 'appointment-payments', 'label' => 'View Appointment Payment'],
            ['name' => 'create-photo-studio-appointment-payments', 'module' => 'appointment-payments', 'label' => 'Create Appointment Payment'],
            ['name' => 'edit-photo-studio-appointment-payments', 'module' => 'appointment-payments', 'label' => 'Edit Appointment Payment'],
            ['name' => 'delete-photo-studio-appointment-payments', 'module' => 'appointment-payments', 'label' => 'Delete Appointment Payment'],
        ];

        $company_role = Role::where('name', 'company')->first();

        foreach ($permission as $perm) {
            $permission_obj = Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'module' => $perm['module'],
                    'label' => $perm['label'],
                    'add_on' => 'PhotoStudioManagement',
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