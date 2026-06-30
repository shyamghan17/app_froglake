<?php

use Illuminate\Support\Facades\Route;
use Workdo\OpticalAndEyeCareCenter\Http\Controllers\DashboardController;
use Workdo\OpticalAndEyeCareCenter\Http\Controllers\EyewearItemController;
use Workdo\OpticalAndEyeCareCenter\Http\Controllers\EyeCareAppoinmentController;
use Workdo\OpticalAndEyeCareCenter\Http\Controllers\EyeTestPrescriptionController;
use Workdo\OpticalAndEyeCareCenter\Http\Controllers\EyePatientController;
use Workdo\OpticalAndEyeCareCenter\Http\Controllers\OpticalDoctorController;
use Workdo\OpticalAndEyeCareCenter\Http\Controllers\EyewearOrderController;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:OpticalAndEyeCareCenter'])->group(function () {
    // Dashboard
    Route::get('/optical-and-eye-care-center/dashboard', [DashboardController::class, 'index'])->name('optical-and-eye-care-center.dashboard');

    // Doctors
    Route::prefix('optical-and-eye-care-center/doctors')->name('optical-and-eye-care-center.optical-doctors.')->group(function () {
        Route::get('/', [OpticalDoctorController::class, 'index'])->name('index');
        Route::post('/', [OpticalDoctorController::class, 'store'])->name('store');
        Route::put('/{opticaldoctor}', [OpticalDoctorController::class, 'update'])->name('update');
        Route::delete('/{opticaldoctor}', [OpticalDoctorController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('optical-and-eye-care-center/eye-patients')->name('optical-and-eye-care-center.eye-patients.')->group(function () {
        Route::get('/', [EyePatientController::class, 'index'])->name('index');
        Route::post('/', [EyePatientController::class, 'store'])->name('store');
        Route::put('/{eyepatient}', [EyePatientController::class, 'update'])->name('update');
        Route::delete('/{eyepatient}', [EyePatientController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('optical-and-eye-care-center/eye-test-prescriptions')->name('optical-and-eye-care-center.eye-test-prescriptions.')->group(function () {
        Route::get('/', [EyeTestPrescriptionController::class, 'index'])->name('index');
        Route::post('/', [EyeTestPrescriptionController::class, 'store'])->name('store');
        Route::put('/{eyetestprescription}', [EyeTestPrescriptionController::class, 'update'])->name('update');
        Route::delete('/{eyetestprescription}', [EyeTestPrescriptionController::class, 'destroy'])->name('destroy');
        Route::get('/{eyetestprescription}/print', [EyeTestPrescriptionController::class, 'print'])->name('print');
    });

    Route::prefix('optical-and-eye-care-center/eye-care-appoinments')->name('optical-and-eye-care-center.eye-care-appoinments.')->group(function () {
        Route::get('/', [EyeCareAppoinmentController::class, 'index'])->name('index');
        Route::post('/', [EyeCareAppoinmentController::class, 'store'])->name('store');
        Route::put('/{eyecareappoinment}', [EyeCareAppoinmentController::class, 'update'])->name('update');
        Route::delete('/{eyecareappoinment}', [EyeCareAppoinmentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('optical-and-eye-care-center/eyewear-items')->name('optical-and-eye-care-center.eyewear-items.')->group(function () {
        Route::get('/', [EyewearItemController::class, 'index'])->name('index');
        Route::get('/create', [EyewearItemController::class, 'create'])->name('create');
        Route::post('/', [EyewearItemController::class, 'store'])->name('store');
        Route::get('/{eyewearitem}', [EyewearItemController::class, 'show'])->name('show');
        Route::get('/{eyewearitem}/edit', [EyewearItemController::class, 'edit'])->name('edit');
        Route::put('/{eyewearitem}', [EyewearItemController::class, 'update'])->name('update');
        Route::delete('/{eyewearitem}', [EyewearItemController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('optical-and-eye-care-center/eyewear-orders')->name('optical-and-eye-care-center.eyewear-orders.')->group(function () {
        Route::get('/', [EyewearOrderController::class, 'index'])->name('index');
        Route::get('/create', [EyewearOrderController::class, 'create'])->name('create');
        Route::post('/', [EyewearOrderController::class, 'store'])->name('store');
        Route::get('/warehouse-products', [EyewearOrderController::class, 'getWarehouseProducts'])->name('warehouse-products');
        Route::get('/{eyewearOrder}', [EyewearOrderController::class, 'show'])->name('show');
        Route::get('/{eyewearOrder}/edit', [EyewearOrderController::class, 'edit'])->name('edit');
        Route::put('/{eyewearOrder}', [EyewearOrderController::class, 'update'])->name('update');
        Route::delete('/{eyewearOrder}', [EyewearOrderController::class, 'destroy'])->name('destroy');
        Route::post('/{eyewearOrder}/post', [EyewearOrderController::class, 'post'])->name('post');
        Route::get('/{eyewearOrder}/print', [EyewearOrderController::class, 'print'])->name('print');
    });
});
