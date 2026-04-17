<?php

use Illuminate\Support\Facades\Route;
use Workdo\SignInWithGoogle\Http\Controllers\GoogleAuthController;
use Workdo\SignInWithGoogle\Http\Controllers\GoogleSettingsController;

// Public Google OAuth routes
Route::middleware(['web'])->group(function () {
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');
});

// Protected Google settings routes
Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:SignInWithGoogle'])->group(function () {
    Route::post('/google-signin/settings/update', [GoogleSettingsController::class, 'update'])->name('google-signin.settings.update');
});