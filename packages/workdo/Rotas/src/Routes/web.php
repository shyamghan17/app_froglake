<?php

use Illuminate\Support\Facades\Route;
use Workdo\Rotas\Http\Controllers\DashboardController;
use Workdo\Rotas\Http\Controllers\RotasDesignationController;
use Workdo\Rotas\Http\Controllers\RotasDepartmentController;
use Workdo\Rotas\Http\Controllers\RotasBranchController;
use Workdo\Rotas\Http\Controllers\RotasLeaveTypeController;
use Workdo\Rotas\Http\Controllers\RotasEmployeeController;
use Workdo\Rotas\Http\Controllers\RotasEmployeeDocumentTypeController;
use Workdo\Rotas\Http\Controllers\RotasLeaveApplicationController;
use Workdo\Rotas\Http\Controllers\RotasLeaveBalanceController;
use Workdo\Rotas\Http\Controllers\RotasShiftController;
use Workdo\Rotas\Http\Controllers\RotasAnnouncementCategoryController;
use Workdo\Rotas\Http\Controllers\RotasAnnouncementController;
use Workdo\Rotas\Http\Controllers\RotasAvailabilityController;
use Workdo\Rotas\Http\Controllers\RotasWorkScheduleController;
use Workdo\Rotas\Http\Controllers\SettingsController;
use Workdo\Rotas\Http\Controllers\RotaController;
use Workdo\Rotas\Http\Middleware\RotaSharedDataMiddleware;


Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:Rotas'])->group(function () {
    Route::get('/rotas/dashboard', [DashboardController::class, 'index'])->name('rotas.dashboard.index');
    Route::get('/rotas', [RotaController::class, 'index'])->name('rotas.index');

    Route::post('/rotas/settings/update', [SettingsController::class, 'update'])->name('rotas.settings.update');
    Route::post('/rotas/settings/update-work-schedule', [SettingsController::class, 'updateWorkSchedule'])->name('rotas.settings.update.work-schedule');

    Route::prefix('rotas/branches')->name('rotas.branches.')->group(function () {
        Route::get('/', [RotasBranchController::class, 'index'])->name('index');
        Route::post('/store', [RotasBranchController::class, 'store'])->name('store');
        Route::put('/{branch}', [RotasBranchController::class, 'update'])->name('update');
        Route::delete('/{branch}', [RotasBranchController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rotas/departments')->name('rotas.departments.')->group(function () {
        Route::get('/', [RotasDepartmentController::class, 'index'])->name('index');
        Route::post('/store', [RotasDepartmentController::class, 'store'])->name('store');
        Route::put('/{department}', [RotasDepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [RotasDepartmentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rotas/designations')->name('rotas.designations.')->group(function () {
        Route::get('/', [RotasDesignationController::class, 'index'])->name('index');
        Route::post('/store', [RotasDesignationController::class, 'store'])->name('store');
        Route::put('/{designation}', [RotasDesignationController::class, 'update'])->name('update');
        Route::delete('/{designation}', [RotasDesignationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rotas/leave-types')->name('rotas.leave-types.')->group(function () {
        Route::get('/', [RotasLeaveTypeController::class, 'index'])->name('index');
        Route::post('/store', [RotasLeaveTypeController::class, 'store'])->name('store');
        Route::put('/{leaveType}', [RotasLeaveTypeController::class, 'update'])->name('update');
        Route::delete('/{leaveType}', [RotasLeaveTypeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rotas/employee-document-types')->name('rotas.employee.document.types.')->group(function () {
        Route::get('/', [RotasEmployeeDocumentTypeController::class, 'index'])->name('index');
        Route::post('/store', [RotasEmployeeDocumentTypeController::class, 'store'])->name('store');
        Route::put('/{employeeDocumentType}', [RotasEmployeeDocumentTypeController::class, 'update'])->name('update');
        Route::delete('/{employeeDocumentType}', [RotasEmployeeDocumentTypeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rotas/shifts')->name('rotas.shifts.')->group(function () {
        Route::get('/', [RotasShiftController::class, 'index'])->name('index');
        Route::post('/', [RotasShiftController::class, 'store'])->name('store');
        Route::get('/{shift}/edit', [RotasShiftController::class, 'edit'])->name('edit');
        Route::put('/{shift}', [RotasShiftController::class, 'update'])->name('update');
        Route::delete('/{shift}', [RotasShiftController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rotas/employees')->name('rotas.employees.')->group(function () {
        Route::get('/', [RotasEmployeeController::class, 'index'])->name('index');
        Route::get('/create', [RotasEmployeeController::class, 'create'])->name('create');
        Route::post('/store', [RotasEmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [RotasEmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [RotasEmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [RotasEmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [RotasEmployeeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rotas/leave-applications')->name('rotas.leave-applications.')->group(function () {
        Route::get('/', [RotasLeaveApplicationController::class, 'index'])->name('index');
        Route::post('/', [RotasLeaveApplicationController::class, 'store'])->name('store');
        Route::get('/{leaveapplication}/edit', [RotasLeaveApplicationController::class, 'edit'])->name('edit');
        Route::put('/{leaveapplication}', [RotasLeaveApplicationController::class, 'update'])->name('update');
        Route::put('/{leaveapplication}/status', [RotasLeaveApplicationController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{leaveapplication}', [RotasLeaveApplicationController::class, 'destroy'])->name('destroy');
    });

    // Delete employee document
    Route::delete('rotas/employees/{employeeId}/documents/{document}', [RotasEmployeeController::class, 'deleteDocument'])->name('rotas.employee-documents.destroy');


    Route::prefix('rotas/leave-balance')->name('rotas.leave-balance.')->group(function () {
        Route::get('/', [RotasLeaveBalanceController::class, 'index'])->name('index');
    });

    // Dependent dropdown routes
    Route::get('rotas/users/{employee}/leave_types', [RotasLeaveApplicationController::class, 'getLeaveTypesByEmployee'])->name('rotas.users.leave_types');
    Route::get('rotas/leave-balance/{employee}/{leaveType}', [RotasLeaveApplicationController::class, 'getLeaveBalance'])->name('rotas.leave-balance');

    Route::prefix('rotas/announcement-categories')->name('rotas.announcement-categories.')->group(function () {
        Route::get('/', [RotasAnnouncementCategoryController::class, 'index'])->name('index');
        Route::post('/store', [RotasAnnouncementCategoryController::class, 'store'])->name('store');
        Route::get('/{announcementcategory}/edit', [RotasAnnouncementCategoryController::class, 'edit'])->name('edit');
        Route::put('/{announcementcategory}', [RotasAnnouncementCategoryController::class, 'update'])->name('update');
        Route::delete('/{announcementcategory}', [RotasAnnouncementCategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rotas/announcements')->name('rotas.announcements.')->group(function () {
        Route::get('/', [RotasAnnouncementController::class, 'index'])->name('index');
        Route::post('/store', [RotasAnnouncementController::class, 'store'])->name('store');
        Route::get('/{announcement}/edit', [RotasAnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{announcement}', [RotasAnnouncementController::class, 'update'])->name('update');
        Route::put('/{announcement}/status', [RotasAnnouncementController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{announcement}', [RotasAnnouncementController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rotas/availabilities')->name('rotas.availabilities.')->group(function () {
        Route::get('/', [RotasAvailabilityController::class, 'index'])->name('index');
        Route::post('/store', [RotasAvailabilityController::class, 'store'])->name('store');
        Route::put('/{availability}', [RotasAvailabilityController::class, 'update'])->name('update');
        Route::delete('/{availability}', [RotasAvailabilityController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rotas/work-schedules')->name('rotas.work-schedules.')->group(function () {
        Route::get('/', [RotasWorkScheduleController::class, 'index'])->name('index');
        Route::put('/{employee}', [RotasWorkScheduleController::class, 'update'])->name('update');
    });


    Route::post('rotas/schedule/save', [RotaController::class, 'store'])->name('rotas.schedule.save');
    Route::put('rotas/schedule/{rota}', [RotaController::class, 'update'])->name('rotas.schedule.update');
    Route::delete('rotas/schedule/{rota}', [RotaController::class, 'destroy'])->name('rotas.schedule.delete');
    Route::post('rotas/share/create', [RotaController::class, 'createShareLink'])->name('rotas.share.create');
    Route::post('rotas/publish-week', [RotaController::class, 'publishWeek'])->name('rotas.publish-week');
    Route::post('rotas/copy-week', [RotaController::class, 'copyWeek'])->name('rotas.copy-week');
    Route::post('rotas/send-mail', [RotaController::class, 'sendMail'])->name('rotas.send-mail');
});

// Public routes for shared schedules (no auth required)
Route::middleware(['web', RotaSharedDataMiddleware::class])->prefix('{userSlug?}/rotas')->name('rotas.frontend.')->group(function () {
    Route::get('/shared/{token}', [RotaController::class, 'viewSharedSchedule'])->name('shared.view');
    Route::post('/shared/{token}/authenticate', [RotaController::class, 'authenticateSharedSchedule'])->name('shared.authenticate');
});
