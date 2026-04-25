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
use Workdo\Esewa\Http\Controllers\EsewaController;

Route::group(['middleware' => ['web', 'auth', 'verified', 'PlanModuleCheck:Esewa']], function () {
    Route::prefix('esewa')->group(function () {
        Route::post('esewa/settings/store', [EsewaController::class, 'setting'])->name('esewa.setting.store');
    });
});

Route::group(['middleware' => ['web']], function () {

    // subscribe plan from company
    Route::post('plan-pay-with/esewa', [EsewaController::class, 'planPayWithESewa'])->name('plan.pay.with.esewa');
    Route::get('plan-get-esewa-status/', [EsewaController::class, 'planGetESewaStatus'])->name('plan.get.esewa.status');

    // invoice pay
    Route::post('invoice-pay-with/esewa', [EsewaController::class, 'invoicePayWithESewa'])->name('invoice.pay.with.esewa');
    Route::get('invoice-get-esewa-status/', [EsewaController::class, 'invoiceGetESewaStatus'])->name('invoice.esewa.status');


    Route::post('course/esewa/{slug?}', [EsewaController::class, 'coursePayWithEsewa'])->name('course.pay.with.esewa');
    Route::get('coursee-get-esewa-status/{slug?}', [EsewaController::class, 'getCoursePaymentStatus'])->name('course.esewa');


    Route::post('/esewa/{slug?}', [EsewaController::class, 'contentPayWithEsewa'])->name('content.pay.with.esewa');
    Route::get('content-get-esewa-status/{slug?}', [EsewaController::class, 'getContentPaymentStatus'])->name('content.esewa');
});
