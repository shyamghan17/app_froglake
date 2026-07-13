<?php

use Illuminate\Support\Facades\Route;
use Workdo\SuggestionBox\Http\Controllers\SuggestionController;
use Workdo\SuggestionBox\Http\Controllers\SuggestionCategoryController;
use Workdo\SuggestionBox\Http\Controllers\SuggestionVoteController;
use Workdo\SuggestionBox\Http\Controllers\SuggestionAdminController;
use Workdo\SuggestionBox\Http\Controllers\SuggestionStatusHistoryController;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:SuggestionBox'])->group(function () {

    Route::prefix('suggestion-box/categories')->name('suggestion-categories.')->group(function () {
        Route::get('/', [SuggestionCategoryController::class, 'index'])->name('index');
        Route::post('/', [SuggestionCategoryController::class, 'store'])->name('store');
        Route::put('/{suggestioncategory}', [SuggestionCategoryController::class, 'update'])->name('update');
        Route::delete('/{suggestioncategory}', [SuggestionCategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('suggestion-box/admin')->name('suggestion-admin.')->group(function () {
        Route::get('/', [SuggestionAdminController::class, 'index'])->name('index');
        Route::post('/{suggestion}/respond', [SuggestionAdminController::class, 'respond'])->name('respond');
    });

    Route::prefix('suggestion-box/suggestions')->name('suggestions.')->group(function () {
        Route::get('/', [SuggestionController::class, 'index'])->name('index');
        Route::get('/my-suggestions', [SuggestionController::class, 'mySuggestions'])->name('my-suggestions');
        Route::post('/', [SuggestionController::class, 'store'])->name('store');
        Route::put('/{suggestion}', [SuggestionController::class, 'update'])->name('update');
        Route::delete('/{suggestion}', [SuggestionController::class, 'destroy'])->name('destroy');
        Route::get('/{suggestion}', [SuggestionController::class, 'show'])->name('show');
        Route::post('/{suggestion}/vote', [SuggestionVoteController::class, 'vote'])->name('vote');
    });

    Route::prefix('suggestion-box/status-histories')->name('status-histories.')->group(function () {
        Route::get('/', [SuggestionStatusHistoryController::class, 'index'])->name('index');
        Route::delete('/{suggestionstatushistory}', [SuggestionStatusHistoryController::class, 'destroy'])->name('destroy');
    });
});