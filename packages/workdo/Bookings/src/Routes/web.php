<?php

use Workdo\Bookings\Http\Controllers\ExtraServiceController;

use Illuminate\Support\Facades\Route;
use Workdo\Bookings\Http\Controllers\DashboardController;
use Workdo\Bookings\Http\Controllers\BookingsItemController;
use Workdo\Bookings\Http\Controllers\BookingController;
use Workdo\Bookings\Http\Controllers\SettingsController;
use Workdo\Bookings\Http\Controllers\ItemsController;
use Workdo\Bookings\Http\Controllers\BookingStaffController;
use Workdo\Bookings\Http\Controllers\BookingPackageController;
use Workdo\Bookings\Http\Controllers\BookingCustomerController;
use Workdo\Bookings\Http\Controllers\BookingAppointmentController;
use Workdo\Bookings\Http\Controllers\BookingCustomPageController;
use Workdo\Bookings\Http\Controllers\BookingExtraServiceController;
use Workdo\Bookings\Http\Controllers\BookingReviewController;
use Workdo\Bookings\Http\Controllers\BookingBusinessHoursController;
use Workdo\Bookings\Http\Controllers\BookingSocialLinkController;
use Workdo\Bookings\Http\Controllers\BookingContactController;
use Workdo\Bookings\Http\Middleware\BookingSharedDataMiddleware;

// Frontend Booking Routes (Public) - Multi-tenant support
Route::middleware(['web', BookingSharedDataMiddleware::class])->prefix('{userSlug?}/booking')->name('booking.')->group(function () {
    Route::get('/', [BookingController::class, 'home'])->name('home');
    Route::get('/about', [BookingController::class, 'about'])->name('about');
    Route::get('/contact', [BookingController::class, 'contact'])->name('contact');
    Route::get('/services', [BookingController::class, 'services'])->name('services');
    Route::get('/services/{id}', [BookingController::class, 'serviceDetail'])->name('services.detail');
    Route::get('/404', [BookingController::class, 'notFound'])->name('404');
    Route::get('/page/{slug}', [BookingController::class, 'customPage'])->name('custom-page');
    Route::get('/social-links', [BookingController::class, 'socialLinks'])->name('social-links');
    Route::get('/custom-pages/active', [BookingCustomPageController::class, 'getActivePages'])->name('custom-pages.active');
    
    Route::post('/contact', [BookingController::class, 'submitContact'])->name('contact.submit');
    Route::post('/contact/store', [BookingController::class, 'storeContact'])->name('contact.store');
    Route::post('/book', [BookingController::class, 'submitBooking'])->name('booking.submit');
    Route::post('/store', [BookingController::class, 'store'])->name('store');
    Route::post('/reviews', [BookingController::class, 'submitReview'])->name('reviews.submit');
    
    // Catch-all route for 404 handling
    Route::fallback([BookingController::class, 'notFound']);
});

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:Bookings'])->group(function () {
    // Dashboard
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/items', [ItemsController::class, 'index'])->name('items.index');
        Route::get('/all-items', [ItemsController::class, 'allItems'])->name('items.all');
        Route::get('/items/create', [ItemsController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemsController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [ItemsController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemsController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [ItemsController::class, 'destroy'])->name('items.destroy');
        
        Route::get('/staff', [BookingStaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [BookingStaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [BookingStaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [BookingStaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{staff}', [BookingStaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{staff}', [BookingStaffController::class, 'destroy'])->name('staff.destroy');
        
        Route::get('/packages', [BookingPackageController::class, 'index'])->name('packages.index');
        Route::post('/packages', [BookingPackageController::class, 'store'])->name('packages.store');
        Route::put('/packages/{package}', [BookingPackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [BookingPackageController::class, 'destroy'])->name('packages.destroy');
        
        Route::get('/customers', [BookingCustomerController::class, 'index'])->name('customers.index');
        Route::post('/customers', [BookingCustomerController::class, 'store'])->name('customers.store');
        Route::put('/customers/{customer}', [BookingCustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [BookingCustomerController::class, 'destroy'])->name('customers.destroy');
        
        Route::get('/appointments', [BookingAppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments', [BookingAppointmentController::class, 'store'])->name('appointments.store');
        Route::put('/appointments/{appointment}', [BookingAppointmentController::class, 'update'])->name('appointments.update');
        Route::put('/appointments/{appointment}/complete', [BookingAppointmentController::class, 'complete'])->name('appointments.complete');
        Route::post('/appointments/store-payment', [BookingAppointmentController::class, 'storePayment'])->name('appointments.store-payment');
        Route::patch('/appointments/{appointment}/status', [BookingAppointmentController::class, 'updateStatus'])->name('appointments.update-status');
        Route::delete('/appointments/{appointment}', [BookingAppointmentController::class, 'destroy'])->name('appointments.destroy');
        Route::get('/appointments/calendar', [BookingAppointmentController::class, 'calendar'])->name('appointments.calendar');
        Route::get('/appointments/kanban', [BookingAppointmentController::class, 'kanban'])->name('appointments.kanban');
        
        Route::get('/payments', [\Workdo\Bookings\Http\Controllers\BookingPaymentController::class, 'index'])->name('payments.index');
        Route::patch('/payments/{payment}/update-status', [\Workdo\Bookings\Http\Controllers\BookingPaymentController::class, 'updateStatus'])->name('payments.update-status');
        
        // System Setup Routes
        Route::get('/brand-settings', [SettingsController::class, 'brandSettingsIndex'])->name('brand-settings.index');
        Route::post('/brand-settings', [SettingsController::class, 'brandSettingsUpdate'])->name('brand-settings.update');
        
        Route::get('/banner-settings', [SettingsController::class, 'bannerSettingsIndex'])->name('banner-settings.index');
        Route::post('/banner-settings', [SettingsController::class, 'bannerSettingsUpdate'])->name('banner-settings.update');
        
        Route::get('/appointment-settings', [SettingsController::class, 'appointmentSettingsIndex'])->name('appointment-settings.index');
        Route::post('/appointment-settings', [SettingsController::class, 'appointmentSettingsUpdate'])->name('appointment-settings.update');
        
        Route::get('/additional-settings', [SettingsController::class, 'additionalSettingsIndex'])->name('additional-settings.index');
        Route::post('/additional-settings', [SettingsController::class, 'additionalSettingsUpdate'])->name('additional-settings.update');
        
        Route::get('/contact-settings', [SettingsController::class, 'contactSettingsIndex'])->name('contact-settings.index');
        Route::post('/contact-settings', [SettingsController::class, 'contactSettingsUpdate'])->name('contact-settings.update');
        
        Route::get('/about-us-settings', [SettingsController::class, 'aboutUsSettingsIndex'])->name('about-us-settings.index');
        Route::post('/about-us-settings', [SettingsController::class, 'aboutUsSettingsUpdate'])->name('about-us-settings.update');
        
        Route::get('/page-sections', [SettingsController::class, 'pageSectionsIndex'])->name('page-sections.index');
        Route::post('/page-sections', [SettingsController::class, 'pageSectionsUpdate'])->name('page-sections.update');
        
        Route::get('/custom-pages', [BookingCustomPageController::class, 'index'])->name('custom-pages.index');
        Route::post('/custom-pages', [BookingCustomPageController::class, 'store'])->name('custom-pages.store');
        Route::get('/custom-pages/{page}', [BookingCustomPageController::class, 'show'])->name('custom-pages.show');
        Route::put('/custom-pages/{page}', [BookingCustomPageController::class, 'update'])->name('custom-pages.update');
        Route::delete('/custom-pages/{page}', [BookingCustomPageController::class, 'destroy'])->name('custom-pages.destroy');
        
        Route::get('/extra-services', [BookingExtraServiceController::class, 'index'])->name('booking-extra-services.index');
        Route::post('/extra-services', [BookingExtraServiceController::class, 'store'])->name('booking-extra-services.store');
        Route::put('/extra-services/{extraservice}', [BookingExtraServiceController::class, 'update'])->name('booking-extra-services.update');
        Route::delete('/extra-services/{extraservice}', [BookingExtraServiceController::class, 'destroy'])->name('booking-extra-services.destroy');
        
        Route::get('/reviews-settings', [BookingReviewController::class, 'index'])->name('reviews-settings.index');
        Route::get('/reviews', [BookingReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews', [BookingReviewController::class, 'store'])->name('reviews.store');
        Route::put('/reviews/{review}', [BookingReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [BookingReviewController::class, 'destroy'])->name('reviews.destroy');
        
        Route::get('/business-hours', [BookingBusinessHoursController::class, 'index'])->name('business-hours.index');
        Route::post('/business-hours', [BookingBusinessHoursController::class, 'store'])->name('business-hours.store');
        Route::put('/business-hours/{day}', [BookingBusinessHoursController::class, 'update'])->name('business-hours.update');
        
        Route::get('/social-links', [BookingSocialLinkController::class, 'index'])->name('social-links.index');
        Route::post('/social-links', [BookingSocialLinkController::class, 'store'])->name('social-links.store');
        Route::put('/social-links/{socialLink}', [BookingSocialLinkController::class, 'update'])->name('social-links.update');
        Route::delete('/social-links/{socialLink}', [BookingSocialLinkController::class, 'destroy'])->name('social-links.destroy');
        
        Route::get('/contacts-settings', [BookingContactController::class, 'index'])->name('contacts-settings.index');
        Route::get('/contacts', [BookingContactController::class, 'index'])->name('contacts.index');
        Route::post('/contacts', [BookingContactController::class, 'store'])->name('contacts.store');
        Route::put('/contacts/{contact}', [BookingContactController::class, 'update'])->name('contacts.update');
        Route::delete('/contacts/{contact}', [BookingContactController::class, 'destroy'])->name('contacts.destroy');
        

    });
});

Route::middleware(['web'])->prefix('bookings')->name('bookings.')->group(function () {
    Route::get('/appointments/time-slots', [BookingAppointmentController::class, 'getAvailableTimeSlots'])->name('appointments.time-slots');
});

// Global fallback for booking routes
Route::middleware(['web'])->prefix('{userSlug?}/booking')->group(function () {
    Route::fallback(function() {
        return redirect()->route('booking.404', ['userSlug' => request()->route('userSlug')]);
    });

    Route::prefix('bookings/extra-services')->name('bookings.extra-services.')->group(function () {
        Route::get('/', [ExtraServiceController::class, 'index'])->name('index');
        Route::post('/', [ExtraServiceController::class, 'store'])->name('store');
        Route::get('/{extraservice}/edit', [ExtraServiceController::class, 'edit'])->name('edit');
        Route::put('/{extraservice}', [ExtraServiceController::class, 'update'])->name('update');
        Route::delete('/{extraservice}', [ExtraServiceController::class, 'destroy'])->name('destroy');
    });
});