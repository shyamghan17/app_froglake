<?php

use Illuminate\Support\Facades\Route;
use Workdo\FindGoogleLeads\Http\Controllers\FindGoogleLeadsController;
use Workdo\FindGoogleLeads\Http\Controllers\FindGoogleLeadsSettingsController;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:FindGoogleLeads'])->group(function () {
    Route::get('/find-google-leads', [FindGoogleLeadsController::class, 'index'])->name('find-google-leads.index');
    
    Route::post('/find-google-leads', [FindGoogleLeadsController::class, 'store'])->name('find-google-leads.store');
    
    Route::delete('/find-google-leads/{lead}', [FindGoogleLeadsController::class, 'destroy'])->name('find-google-leads.destroy');
    
    Route::get('/find-google-leads/{lead}', [FindGoogleLeadsController::class, 'show'])->name('find-google-leads.show');
    
    Route::delete('/find-google-leads/contacts/{contact}', [FindGoogleLeadsController::class, 'destroyContact'])->name('find-google-leads.contacts.destroy');
        
    Route::post('/findgoogleleads/settings', [FindGoogleLeadsSettingsController::class, 'update'])->name('findgoogleleads.settings.update');
    
    Route::post('/findgoogleleads/get-stages', [FindGoogleLeadsSettingsController::class, 'getStages'])->name('findgooglelead.setting.get.stage');
    
    Route::get('/findgoogleleads/pipelines-stages', [FindGoogleLeadsSettingsController::class, 'getPipelinesAndStages'])->name('findgoogleleads.pipelines.stages');
});