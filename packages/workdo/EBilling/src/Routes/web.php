<?php

use Illuminate\Support\Facades\Route;
use Workdo\EBilling\Http\Controllers\DashboardController;
use Workdo\EBilling\Http\Controllers\EBillingItemController;
use Workdo\EBilling\Http\Controllers\EBillingSettingsController;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:EBilling'])->group(function () {
    Route::get('/ebilling', [DashboardController::class, 'index'])->name('ebilling.index');

    Route::post('/ebilling/settings', [EBillingSettingsController::class, 'update'])->name('ebilling.settings.update');

    Route::prefix('ebilling/items')->name('ebilling.items.')->group(function () {
        Route::get('/', [EBillingItemController::class, 'index'])->name('index');
        Route::get('/create', [EBillingItemController::class, 'create'])->name('create');
        Route::post('/', [EBillingItemController::class, 'store'])->name('store');
        Route::get('/{item}/edit', [EBillingItemController::class, 'edit'])->name('edit');
        Route::put('/{item}', [EBillingItemController::class, 'update'])->name('update');
        Route::delete('/{item}', [EBillingItemController::class, 'destroy'])->name('destroy');
    });
});
