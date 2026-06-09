<?php

use Illuminate\Support\Facades\Route;
use Workdo\Khalti\Http\Controllers\KhaltiSettingsController;
use Workdo\Khalti\Http\Controllers\KhaltiController;

Route::middleware('web')->group(function () {
    // Settings
    Route::post('/settings/khalti', [KhaltiSettingsController::class, 'update'])->name('khalti.settings.update')->middleware(['auth', 'verified', 'PlanModuleCheck:Khalti']);

    // Plan Payments
    Route::post('/payment/khalti/plan', [KhaltiController::class, 'planPayWithKhalti'])->name('khalti.plan.pay')->middleware(['auth', 'verified']);
    Route::get('/payment/khalti/plan/status', [KhaltiController::class, 'planGetKhaltiStatus'])->name('khalti.plan.status');

    // Booking Payments
    Route::post('/payment/khalti/booking/{userSlug}', [KhaltiController::class, 'bookingPayWithKhalti'])->name('khalti.booking.payment.pay');
    Route::get('/payment/khalti/booking/{userSlug}/status', [KhaltiController::class, 'bookingGetKhaltiStatus'])->name('khalti.booking.payment.status');

    // Beauty Spa Payments
    Route::post('/payment/khalti/beauty-spa/{userSlug}', [KhaltiController::class, 'beautySpaPayWithKhalti'])->name('khalti.beauty-spa.payment.pay');
    Route::get('/payment/khalti/beauty-spa/{userSlug}/status', [KhaltiController::class, 'beautySpaGetKhaltiStatus'])->name('khalti.beauty-spa.payment.status');

    // LMS Payments
    Route::post('/payment/khalti/lms/{userSlug}', [KhaltiController::class, 'lmsPayWithKhalti'])->name('khalti.lms.payment.pay');
    Route::get('/payment/khalti/lms/{userSlug}/status', [KhaltiController::class, 'lmsGetKhaltiStatus'])->name('khalti.lms.payment.status');

    // Laundry Payments
    Route::post('/payment/khalti/laundry/{userSlug}', [KhaltiController::class, 'laundryPayWithKhalti'])->name('khalti.laundry.payment.pay');
    Route::get('/payment/khalti/laundry/{userSlug}/status', [KhaltiController::class, 'laundryGetKhaltiStatus'])->name('khalti.laundry.payment.status');

    // Parking Payments
    Route::post('/payment/khalti/parking/{userSlug}', [KhaltiController::class, 'parkingPayWithKhalti'])->name('khalti.parking.payment.pay');
    Route::get('/payment/khalti/parking/{userSlug}/status', [KhaltiController::class, 'parkingGetKhaltiStatus'])->name('khalti.parking.payment.status');

    // Events Payments
    Route::post('/payment/khalti/events/{userSlug}', [KhaltiController::class, 'eventsPayWithKhalti'])->name('khalti.events-management.payment.pay');
    Route::get('/payment/khalti/events/{userSlug}/status', [KhaltiController::class, 'eventsGetKhaltiStatus'])->name('khalti.events-management.payment.status');

    // Holidayz Payments
    Route::post('/payment/khalti/holidayz/{userSlug}', [KhaltiController::class, 'holidayzPayWithKhalti'])->name('khalti.holidayz.payment.pay');
    Route::get('/payment/khalti/holidayz/{userSlug}/status', [KhaltiController::class, 'holidayzGetKhaltiStatus'])->name('khalti.holidayz.payment.status');
});
