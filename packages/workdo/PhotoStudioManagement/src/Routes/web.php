<?php

use Illuminate\Support\Facades\Route;
use Workdo\PhotoStudioManagement\Http\Controllers\DashboardController;
use Workdo\PhotoStudioManagement\Http\Controllers\FrontendController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioSetupController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioGalleryTypeController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioServiceCategoryController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioCustomPageController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioEquipmentTagController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioTeamMemberController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioEquipmentTypeController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioCameraKitController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioServiceController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioContactController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioSubscriberController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioAppointmentController;
use Workdo\PhotoStudioManagement\Http\Controllers\PhotoStudioAppointmentPaymentController;
use Workdo\PhotoStudioManagement\Http\Middleware\PhotoStudioSharedDataMiddleware;
// Admin Routes
Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:PhotoStudioManagement'])->group(function () {
    Route::get('/photo-studio-management', [DashboardController::class, 'index'])->name('photo-studio-management.index');

    // Brand Settings
    Route::prefix('photo-studio-management/brand-settings')->name('photo-studio-management.brand-settings.')->group(function () {
        Route::get('/', [PhotoStudioSetupController::class, 'brandIndex'])->name('index');
        Route::post('/', [PhotoStudioSetupController::class, 'brandStore'])->name('store');
    });

    // Testimonials
    Route::prefix('photo-studio-management/testimonials')->name('photo-studio-management.testimonials.')->group(function () {
        Route::get('/', [PhotoStudioSetupController::class, 'testimonialsIndex'])->name('index');
        Route::post('/', [PhotoStudioSetupController::class, 'testimonialsStore'])->name('store');
    });

    // Equipment Tags
    Route::resource('photo-studio-management/equipment-tags', PhotoStudioEquipmentTagController::class)
        ->names('photo-studio-management.equipment-tags')
        ->except(['create', 'show', 'edit']);

    // Equipment Types
    Route::resource('photo-studio-management/equipment-types', PhotoStudioEquipmentTypeController::class)
        ->names('photo-studio-management.equipment-types')
        ->except(['create', 'show', 'edit']);

    // Custom Pages
    Route::resource('photo-studio-management/custom-pages', PhotoStudioCustomPageController::class)
        ->names('photo-studio-management.custom-pages')
        ->except(['create', 'show', 'edit']);

    // Service Categories
    Route::resource('photo-studio-management/service-categories', PhotoStudioServiceCategoryController::class)
        ->names('photo-studio-management.service-categories')
        ->except(['create', 'show', 'edit']);

    // Gallery Types
    Route::resource('photo-studio-management/gallery-types', PhotoStudioGalleryTypeController::class)
        ->names('photo-studio-management.gallery-types')
        ->except(['create', 'show', 'edit']);

    // FAQ Section
    Route::prefix('photo-studio-management/faqs')->name('photo-studio-management.faqs.')->group(function () {
        Route::get('/', [PhotoStudioSetupController::class, 'faqIndex'])->name('index');
        Route::post('/', [PhotoStudioSetupController::class, 'faqStore'])->name('store');
    });

    // Media Section
    Route::prefix('photo-studio-management/media-section')->name('photo-studio-management.media-section.')->group(function () {
        Route::get('/', [PhotoStudioSetupController::class, 'mediaIndex'])->name('index');
        Route::post('/', [PhotoStudioSetupController::class, 'mediaStore'])->name('store');
    });

    // Award Section
    Route::prefix('photo-studio-management/award-section')->name('photo-studio-management.award-section.')->group(function () {
        Route::get('/', [PhotoStudioSetupController::class, 'awardIndex'])->name('index');
        Route::post('/', [PhotoStudioSetupController::class, 'awardStore'])->name('store');
    });

    // Gallery Section
    Route::prefix('photo-studio-management/gallery-section')->name('photo-studio-management.gallery-section.')->group(function () {
        Route::get('/', [PhotoStudioSetupController::class, 'galleryIndex'])->name('index');
        Route::post('/', [PhotoStudioSetupController::class, 'galleryStore'])->name('store');
    });

    // Title Section
    Route::prefix('photo-studio-management/title-section')->name('photo-studio-management.title-section.')->group(function () {
        Route::get('/', [PhotoStudioSetupController::class, 'titleIndex'])->name('index');
        Route::post('/', [PhotoStudioSetupController::class, 'titleStore'])->name('store');
    });

    // About Section
    Route::prefix('photo-studio-management/about-section')->name('photo-studio-management.about-section.')->group(function () {
        Route::get('/', [PhotoStudioSetupController::class, 'aboutIndex'])->name('index');
        Route::post('/', [PhotoStudioSetupController::class, 'aboutStore'])->name('store');
    });

    // Footer Section
    Route::prefix('photo-studio-management/footer-section')->name('photo-studio-management.footer-section.')->group(function () {
        Route::get('/', [PhotoStudioSetupController::class, 'footerIndex'])->name('index');
        Route::post('/', [PhotoStudioSetupController::class, 'footerStore'])->name('store');
    });

    // Contact Section
    Route::prefix('photo-studio-management/contact-section')->name('photo-studio-management.contact-section.')->group(function () {
        Route::get('/', [PhotoStudioSetupController::class, 'contactIndex'])->name('index');
        Route::post('/', [PhotoStudioSetupController::class, 'contactStore'])->name('store');
    });

    // Banner Section
    Route::prefix('photo-studio-management/banner-section')->name('photo-studio-management.banner-section.')->group(function () {
        Route::get('/', [PhotoStudioSetupController::class, 'bannerIndex'])->name('index');
        Route::post('/', [PhotoStudioSetupController::class, 'bannerStore'])->name('store');
    });

    // Dashboard Welcome Card
    Route::post('photo-studio-management/dashboard-welcome-card', [PhotoStudioSetupController::class, 'dashboardWelcomeCardStore'])->name('photo-studio-management.dashboard-welcome-card.store');

    // Team Members
    Route::resource('photo-studio-management/team-members', PhotoStudioTeamMemberController::class)
        ->names('photo-studio-management.team-members')
        ->except(['create', 'show', 'edit']);

    // Camera Kits
    Route::resource('photo-studio-management/camera-kits', PhotoStudioCameraKitController::class)
        ->names('photo-studio-management.camera-kits')
        ->except(['create', 'show', 'edit']);

    // Services
    Route::resource('photo-studio-management/services', PhotoStudioServiceController::class)
        ->names('photo-studio-management.services')
        ->except(['create', 'show', 'edit']);

    // Subscribers
    Route::prefix('photo-studio-management/subscribers')->name('photo-studio-management.subscribers.')->group(function () {
        Route::get('/', [PhotoStudioSubscriberController::class, 'index'])->name('index');
        Route::delete('/{subscriber}', [PhotoStudioSubscriberController::class, 'destroy'])->name('destroy');
    });

    // Contacts
    Route::prefix('photo-studio-management/contacts')->name('photo-studio-management.contacts.')->group(function () {
        Route::get('/', [PhotoStudioContactController::class, 'index'])->name('index');
        Route::delete('/{contact}', [PhotoStudioContactController::class, 'destroy'])->name('destroy');
    });

    // Appointments
    Route::prefix('photo-studio-management/appointments')->name('photo-studio-management.appointments.')->group(function () {
        Route::get('/', [PhotoStudioAppointmentController::class, 'index'])->name('index');
        Route::post('/', [PhotoStudioAppointmentController::class, 'store'])->name('store');
        Route::put('/{appointment}', [PhotoStudioAppointmentController::class, 'update'])->name('update');
        Route::post('/{appointment}/assign-team-members', [PhotoStudioAppointmentController::class, 'assignTeamMembers'])->name('assign-team-members');
        Route::patch('/{appointment}/status', [PhotoStudioAppointmentController::class, 'updateStatus'])->name('status');
        Route::delete('/{appointment}', [PhotoStudioAppointmentController::class, 'destroy'])->name('destroy');
    });

    // Appointment Payments
    Route::prefix('photo-studio-management/appointment-payments')->name('photo-studio-management.appointment-payments.')->group(function () {
        Route::get('/', [PhotoStudioAppointmentPaymentController::class, 'index'])->name('index');
        Route::post('/', [PhotoStudioAppointmentPaymentController::class, 'store'])->name('store');
        Route::put('/{payment}/status', [PhotoStudioAppointmentPaymentController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{payment}', [PhotoStudioAppointmentPaymentController::class, 'destroy'])->name('destroy');
    });
});

// Frontend Routes - Public
Route::middleware(['web', PhotoStudioSharedDataMiddleware::class])->prefix('photostudio-management/{userSlug}')->name('photo-studio-management.frontend.')->group(function () {
    Route::get('/', [FrontendController::class, 'index'])->name('index');
    Route::get('/services', [FrontendController::class, 'services'])->name('services');
    Route::get('/portfolio', [FrontendController::class, 'portfolio'])->name('portfolio');
    Route::get('/appointment', [FrontendController::class, 'appointment'])->name('appointment');
    Route::get('/faq', [FrontendController::class, 'faq'])->name('faq');
    Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
    Route::get('/media-awards', [FrontendController::class, 'mediaAwards'])->name('media-awards');
    Route::get('/camera-kit', [FrontendController::class, 'cameraKit'])->name('camera-kit');
    Route::post('/contact', [FrontendController::class, 'storeContact'])->name('contact.store');
    Route::post('/newsletter', [FrontendController::class, 'storeNewsletter'])->name('newsletter.store');
    Route::get('/page/{slug}', [FrontendController::class, 'customPage'])->name('custom-page');
});
