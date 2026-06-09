<?php

use Illuminate\Support\Facades\Route;
use Workdo\SMS\Http\Controllers\SMSSettingsController;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:SMS'])->group(function () {
    Route::get('sms/settings', [SMSSettingsController::class, 'index'])->name('sms.settings.index');
    Route::post('sms/settings/store', [SMSSettingsController::class, 'store'])->name('sms.settings.store');
});