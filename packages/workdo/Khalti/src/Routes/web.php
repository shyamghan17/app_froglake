<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Workdo\Khalti\Http\Controllers\KhaltiController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:Khalti']], function () {
    Route::prefix('khalti')->group(function () {
        Route::post('/setting/store', [KhaltiController::class, 'setting'])->name('khalti.setting.store');
    });
});

Route::group(['middleware' => 'web'], function () {
    Route::post('plan-pay-with-khalti', [KhaltiController::class, 'planPayWithKhalti'])->name('plan.pay.with.khalti');
    Route::post('plan-get-khalti-status', [KhaltiController::class, 'planGetKhaltiStatus'])->name('plan.get.khalti.status');

    // beauty spa
    Route::post('/beauty-spa-pay-with-khalti/{slug?}', [KhaltiController::class, 'BeautySpaPayWithKhalti'])->name('beauty.spa.pay.with.khalti');
    Route::post('/beauty-spa/khalti/{slug?}', [KhaltiController::class, 'getBeautySpaPaymentStatus'])->name('beauty.spa.khalti.status');

    Route::post('course-pay-with-khalti/{slug?}', [KhaltiController::class, 'coursePayWithKhalti'])->name('course.pay.with.khalti');

    Route::post('/invoice-khalti', [KhaltiController::class, 'getInvoicePaymentStatus'])->name('invoice.khalti');


    // tvstudio
    Route::post('content-pay-with-khalti/{slug?}', [KhaltiController::class, 'contentPayWithKhalti'])->name('content.pay.with.khalti');

    // facilites
    // booking
    Route::post('/facilities-pay-with-khalti/{slug?}', [KhaltiController::class, 'FacilitiesPayWithKhalti'])->name('facilities.pay.with.khalti');
    Route::post('/facilities/khalti/{slug?}', [KhaltiController::class, 'getFacilitiesPaymentStatus'])->name('facilities.khalti.status');

    // event booking
    Route::post('/event-show-booking-pay-with-khalti/{slug?}', [KhaltiController::class, 'EventShowBookingPayWithKhalti'])->name('event.show.booking.pay.with.khalti');
    Route::post('/event-show-booking/khalti/{slug?}', [KhaltiController::class, 'getEventShowBookingPaymentStatus'])->name('event.show.booking.khalti.status');

    Route::prefix('hotel/{slug}')->group(function () {
        Route::post('pay-with/kahlti', [KhaltiController::class, 'BookingPayWithKhalti'])->name('hotel.pay.with.khalti');
        Route::post('get-khalti-payment-status', [KhaltiController::class, 'GetBookingPaymentStatus'])->name('booking.get.khalti.status');
    });

    // gym management
    Route::post('/memberplan-pay-with-khalti', [KhaltiController::class, 'memberplanPayWithKhalti'])->name('memberplan.pay.with.khalti');

});

