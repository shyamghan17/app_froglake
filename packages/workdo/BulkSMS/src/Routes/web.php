<?php

use Workdo\BulkSMS\Http\Controllers\SingleSmsController;
use Workdo\BulkSMS\Http\Controllers\BulksmsGroupController;
use Workdo\BulkSMS\Http\Controllers\BulksmsGroupSmsController;
use Workdo\BulkSMS\Http\Controllers\BulkSmsContactController;
use Illuminate\Support\Facades\Route;
use Workdo\BulkSMS\Http\Controllers\BulkSMSSettingsController;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:BulkSMS'])->group(function () {

    Route::post('/bulk-sms/settings', [BulkSMSSettingsController::class, 'store'])->name('bulksms.settings.store');

    Route::prefix('bulk-s-m-s/bulk-sms-contacts')->name('bulk-s-m-s.bulk-sms-contacts.')->group(function () {
        Route::get('/', [BulkSmsContactController::class, 'index'])->name('index');
        Route::post('/', [BulkSmsContactController::class, 'store'])->name('store');
        Route::put('/{bulksmscontact}', [BulkSmsContactController::class, 'update'])->name('update');
        Route::delete('/{bulksmscontact}', [BulkSmsContactController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('bulk-s-m-s/bulksms-group-sms')->name('bulk-s-m-s.bulksms-group-sms.')->group(function () {
        Route::get('/', [BulksmsGroupSmsController::class, 'index'])->name('index');
        Route::post('/', [BulksmsGroupSmsController::class, 'store'])->name('store');
        Route::get('/{bulksmsgroupsms}', [BulksmsGroupSmsController::class, 'show'])->name('show');
        Route::delete('/{bulksmsgroupsms}', [BulksmsGroupSmsController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('bulk-s-m-s/bulksms-send-messages')->name('bulk-s-m-s.bulksms-send-messages.')->group(function () {
        Route::delete('/{bulksmssendmessage}', [BulksmsGroupSmsController::class, 'smsdestroy'])->name('destroy');
    });


    Route::prefix('bulk-s-m-s/bulk-sms-groups')->name('bulk-s-m-s.bulk-sms-groups.')->group(function () {
        Route::get('/', [BulksmsGroupController::class, 'index'])->name('index');
        Route::post('/', [BulksmsGroupController::class, 'store'])->name('store');
        Route::put('/{bulksmsgroup}', [BulksmsGroupController::class, 'update'])->name('update');
        Route::delete('/{bulksmsgroup}', [BulksmsGroupController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('bulk-s-m-s/single-sms')->name('bulk-s-m-s.single-sms.')->group(function () {
        Route::get('/', [SingleSmsController::class, 'index'])->name('index');
        Route::post('/', [SingleSmsController::class, 'store'])->name('store');
        Route::delete('/{singlesms}', [SingleSmsController::class, 'destroy'])->name('destroy');
    });
});