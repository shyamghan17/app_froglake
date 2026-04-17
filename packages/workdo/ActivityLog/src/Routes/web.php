<?php

use Illuminate\Support\Facades\Route;
use Workdo\ActivityLog\Http\Controllers\ActivityLogController;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:ActivityLog'])->group(function () {
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::delete('/activity-logs/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
});