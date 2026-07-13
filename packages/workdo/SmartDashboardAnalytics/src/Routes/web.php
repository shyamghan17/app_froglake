<?php

use Illuminate\Support\Facades\Route;
use Workdo\SmartDashboardAnalytics\Http\Controllers\ExecutiveDashboardController;
use Workdo\SmartDashboardAnalytics\Http\Controllers\FinancialAnalyticsController;
use Workdo\SmartDashboardAnalytics\Http\Controllers\TeamPerformanceController;
use Workdo\SmartDashboardAnalytics\Http\Controllers\SalesAnalyticsController;
use Workdo\SmartDashboardAnalytics\Http\Controllers\OperationalAnalyticsController;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:SmartDashboardAnalytics'])->group(function () {
    
    // Page 1: Executive Overview Dashboard
    Route::get('/smart-analytics/dashboard', [ExecutiveDashboardController::class, 'index'])->name('smart-analytics.dashboard');
    
    // Page 2: Financial Analytics Dashboard
    Route::get('/smart-analytics/financial', [FinancialAnalyticsController::class, 'index'])->name('smart-analytics.financial');
    Route::get('/smart-analytics/financial/revenue-txns', [FinancialAnalyticsController::class, 'revenueTransactions'])->name('smart-analytics.financial.revenue-txns');
    Route::get('/smart-analytics/financial/expense-txns', [FinancialAnalyticsController::class, 'expenseTransactions'])->name('smart-analytics.financial.expense-txns');
    Route::get('/smart-analytics/financial/journal-txns', [FinancialAnalyticsController::class, 'journalEntries'])->name('smart-analytics.financial.journal-txns');
    
    // Page 3: Team Performance Dashboard
    Route::get('/smart-analytics/team', [TeamPerformanceController::class, 'index'])->name('smart-analytics.team');
    
    // Page 4: Sales & Customer Analytics
    Route::get('/smart-analytics/sales', [SalesAnalyticsController::class, 'index'])->name('smart-analytics.sales');
    
    // Page 5: Operational Analytics
    Route::get('/smart-analytics/operations', [OperationalAnalyticsController::class, 'index'])->name('smart-analytics.operations');
    Route::get('/smart-analytics/operations/pos-detail/{posId}', [OperationalAnalyticsController::class, 'posDetail'])->name('smart-analytics.operations.pos-detail');
});
