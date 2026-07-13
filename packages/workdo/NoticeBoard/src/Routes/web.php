<?php

use Workdo\NoticeBoard\Http\Controllers\NoticeController;
use Workdo\NoticeBoard\Http\Controllers\NoticeCommentController;

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:NoticeBoard'])->group(function () {
    // Board
    Route::prefix('notice-board')->name('notice-board.')->group(function () {
        Route::get('/board', [NoticeController::class, 'board'])->name('board');
        Route::get('/critical-alerts', [NoticeController::class, 'criticalAlerts'])->name('critical-alerts');
        Route::patch('/{notice}/mark-read', [NoticeController::class, 'markRead'])->name('mark-read');
        Route::patch('/{notice}/acknowledge', [NoticeController::class, 'acknowledge'])->name('acknowledge');
    });

    // Notices
    Route::prefix('notice-board/notices')->name('notice-board.notices.')->group(function () {
        Route::get('/', [NoticeController::class, 'index'])->name('index');
        Route::get('/target-options', [NoticeController::class, 'targetOptions'])->name('target-options');
        Route::post('/', [NoticeController::class, 'store'])->name('store');
        Route::put('/{notice}', [NoticeController::class, 'update'])->name('update');
        Route::delete('/{notice}', [NoticeController::class, 'destroy'])->name('destroy');
        Route::patch('/{notice}/toggle-pin', [NoticeController::class, 'togglePin'])->name('toggle-pin');
        Route::patch('/{notice}/publish', [NoticeController::class, 'publish'])->name('publish');
        Route::patch('/{notice}/deactivate', [NoticeController::class, 'deactivate'])->name('deactivate');

        // Comments
        Route::prefix('/{notice}/comments')->name('comments.')->group(function () {
            Route::post('/', [NoticeCommentController::class, 'store'])->name('store');
            Route::post('/{comment}/reply', [NoticeCommentController::class, 'reply'])->name('reply');
            Route::delete('/{comment}', [NoticeCommentController::class, 'destroy'])->name('destroy');
        });

        Route::get('/{notice}', [NoticeController::class, 'show'])->name('show');
    });
});
