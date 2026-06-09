<?php

use Illuminate\Support\Facades\Route;
use Workdo\Portfolio\Http\Controllers\PortfolioCategoryController;
use Workdo\Portfolio\Http\Controllers\PortfolioController;
use Workdo\Portfolio\Http\Controllers\PortfolioFrontendController;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:Portfolio'])->group(function () {
    Route::prefix('portfolio/categories')->name('portfolio.categories.')->group(function () {
        Route::get('/', [PortfolioCategoryController::class, 'index'])->name('index');
        Route::post('/', [PortfolioCategoryController::class, 'store'])->name('store');
        Route::put('/{portfoliocategory}', [PortfolioCategoryController::class, 'update'])->name('update');
        Route::delete('/{portfoliocategory}', [PortfolioCategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('portfolio/portfolios')->name('portfolio.portfolios.')->group(function () {
        Route::get('/', [PortfolioController::class, 'index'])->name('index');
        Route::get('/create', [PortfolioController::class, 'create'])->name('create');
        Route::post('/', [PortfolioController::class, 'store'])->name('store');
        Route::get('/{portfolio}/edit', [PortfolioController::class, 'edit'])->name('edit');
        Route::put('/{portfolio}', [PortfolioController::class, 'update'])->name('update');
        Route::delete('/{portfolio}', [PortfolioController::class, 'destroy'])->name('destroy');
    });
});

// Public portfolio routes
Route::middleware(['web'])->group(function () {
    Route::get('/portfolio/{slug}', [PortfolioFrontendController::class, 'show'])->name('portfolio.show');
});
