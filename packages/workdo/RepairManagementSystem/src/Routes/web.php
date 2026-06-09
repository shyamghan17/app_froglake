<?php

use Workdo\RepairManagementSystem\Http\Controllers\RepairInvoiceController;
use Workdo\RepairManagementSystem\Http\Controllers\RepairWarrantyController;
use Workdo\RepairManagementSystem\Http\Controllers\RepairOrderRequestController;
use Workdo\RepairManagementSystem\Http\Controllers\RepairTechnicianController;
use Workdo\RepairManagementSystem\Http\Controllers\RepairProductPartController;
use Illuminate\Support\Facades\Route;
use Workdo\RepairManagementSystem\Http\Controllers\RepairMovementHistoryController;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:RepairManagementSystem'])->group(function () {
    Route::resource('repair-technicians', RepairTechnicianController::class)
        ->names('repair-management-system.repair-technicians');

    Route::resource('repair-order-requests', RepairOrderRequestController::class)
        ->names('repair-management-system.repair-order-requests');
    Route::get('repair-order-requests/{repair_order_request}/steps/{response}', [RepairOrderRequestController::class, 'repairOrderStepsChange'])
        ->name('repair-management-system.repair-order-requests.steps-change');
    
    Route::get('repair-order-requests/{id}/movement-history', [RepairMovementHistoryController::class, 'index'])
        ->name('repair-management-system.repair-order-requests.movement-history');
    
    Route::prefix('repair-management-system/repair-product-parts')->name('repair-management-system.repair-product-parts.')->group(function () {
        Route::get('/{id}', [RepairProductPartController::class, 'index'])->name('index');
        Route::post('/store', [RepairProductPartController::class, 'store'])->name('store');
        Route::post('/product', [RepairProductPartController::class, 'product'])->name('product');
        Route::delete('/{id}', [RepairProductPartController::class, 'destroy'])->name('destroy');
    });

    Route::resource('repair-warranties', RepairWarrantyController::class)
        ->names('repair-management-system.repair-warranties');

    Route::get('repair-order-requests/{repair_order}/parts', [RepairWarrantyController::class, 'getPartsByRepairOrder'])
        ->name('repair-management-system.repair-order-requests.parts');

    Route::post('repair-invoices/create-from-order/{repair_order}', [RepairInvoiceController::class, 'createInvoice'])
        ->name('repair-management-system.repair-invoices.create-from-order');

    Route::prefix('repair-management-system/repair-invoices')->name('repair-management-system.repair-invoices.')->group(function () {
        Route::get('/', [RepairInvoiceController::class, 'index'])->name('index');
        Route::get('/{repairinvoice}', [RepairInvoiceController::class, 'show'])->name('show');

        Route::get('/{repairinvoice}/payment', [RepairInvoiceController::class, 'makePayment'])->name('payment');
        Route::get('/{repairinvoice}/payment-history', [RepairInvoiceController::class, 'paymentHistory'])->name('payment-history');
        Route::get('/{repairinvoice}/print', [RepairInvoiceController::class, 'print'])->name('print');
        Route::delete('/{repairinvoice}', [RepairInvoiceController::class, 'destroy'])->name('destroy');
    });
});