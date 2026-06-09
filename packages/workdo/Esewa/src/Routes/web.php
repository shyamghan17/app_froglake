<?php

use Illuminate\Support\Facades\Route;
use Workdo\Esewa\Http\Controllers\EsewaController;
use Workdo\Esewa\Http\Controllers\EsewaSettingsController;

Route::middleware('web')->prefix('esewa')->name('esewa.')->group(function () {
    // Settings
    Route::post('settings', [EsewaSettingsController::class, 'update'])->middleware(['auth', 'verified', 'PlanModuleCheck:Esewa'])->name('settings.update');

    // Checkout
    Route::get('checkout/{data}', [EsewaSettingsController::class, 'checkout'])->name('checkout');

    // Plan Payments
    Route::post('plan/payment', [EsewaController::class, 'planPayWithEsewa'])->middleware(['auth', 'verified'])->name('plan.pay');
    Route::get('plan/status/{order_id}', [EsewaController::class, 'planGetEsewaStatus'])->middleware(['auth', 'verified'])->name('plan.status');

    // Booking payments
    Route::post('{userSlug?}/booking/payment', [EsewaController::class, 'bookingPayWithEsewa'])->name('booking.payment.pay');
    Route::get('{userSlug?}/booking/status/{order_id}', [EsewaController::class, 'bookingGetEsewaStatus'])->name('booking.payment.status');

    // Beauty Spa payments
    Route::post('{userSlug?}/beauty-spa/payment', [EsewaController::class, 'beautySpaPayWithEsewa'])->name('beauty-spa.payment.pay');
    Route::get('{userSlug?}/beauty-spa/status/{order_id}', [EsewaController::class, 'beautySpaGetEsewaStatus'])->name('beauty-spa.payment.status');

    // LMS payments
    Route::post('{userSlug?}/lms/payment', [EsewaController::class, 'lmsPayWithEsewa'])->name('lms.payment.pay');
    Route::get('{userSlug?}/lms/status/{order_id}', [EsewaController::class, 'lmsGetEsewaStatus'])->name('lms.payment.status');

    // Laundry payments
    Route::post('{userSlug?}/laundry/payment', [EsewaController::class, 'laundryPayWithEsewa'])->name('laundry.payment.pay');
    Route::get('{userSlug?}/laundry/status/{order_id}', [EsewaController::class, 'laundryGetEsewaStatus'])->name('laundry.payment.status');

    // Parking payments
    Route::post('{userSlug}/parking/payment', [EsewaController::class, 'parkingPayWithEsewa'])->name('parking.payment.pay');
    Route::get('{userSlug}/parking/status/{order_id}', [EsewaController::class, 'parkingGetEsewaStatus'])->name('parking.payment.status');

    // Events payments
    Route::post('{userSlug?}/events/payment', [EsewaController::class, 'eventsPayWithEsewa'])->name('events-management.payment.pay');
    Route::get('{userSlug?}/events/status/{order_id}', [EsewaController::class, 'eventsGetEsewaStatus'])->name('events-management.payment.status');

    // Holidayz payments
    Route::post('{userSlug?}/holidayz/payment', [EsewaController::class, 'holidayzPayWithEsewa'])->name('holidayz.payment.pay');
    Route::get('{userSlug?}/holidayz/status/{order_id}', [EsewaController::class, 'holidayzGetEsewaStatus'])->name('holidayz.payment.status');

   });
