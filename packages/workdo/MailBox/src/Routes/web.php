<?php

use Illuminate\Support\Facades\Route;
use Workdo\MailBox\Http\Controllers\MailBoxController;
use Workdo\MailBox\Http\Controllers\MailBoxCredentialController;

Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:MailBox'])->group(function () {
    Route::prefix('mailbox')->name('mailbox.')->group(function () {
        // Credential Management
        Route::prefix('credentials')->name('credentials.')->group(function () {
            Route::get('/configuration', [MailBoxCredentialController::class, 'configuration'])->name('configuration');
            Route::post('/configuration', [MailBoxCredentialController::class, 'store'])->name('store');
            Route::post('/test-connection', [MailBoxCredentialController::class, 'testConnection'])->name('test.connection');
            Route::post('/quick-setup', [MailBoxCredentialController::class, 'quickSetup'])->name('quick.setup');
            Route::post('/switch-account/{id}', [MailBoxCredentialController::class, 'switchAccount'])->name('switch.account');
            Route::delete('/delete-account/{id}', [MailBoxCredentialController::class, 'deleteAccount'])->name('delete.account');
        });
        
        // Email folders
        Route::get('/inbox', [MailBoxController::class, 'index'])->defaults('folder', 'inbox')->name('inbox');
        Route::get('/sent', [MailBoxController::class, 'index'])->defaults('folder', 'sent')->name('sent');
        Route::get('/drafts', [MailBoxController::class, 'index'])->defaults('folder', 'drafts')->name('drafts');
        Route::get('/trash', [MailBoxController::class, 'index'])->defaults('folder', 'trash')->name('trash');
        Route::get('/spam', [MailBoxController::class, 'index'])->defaults('folder', 'spam')->name('spam');
        Route::get('/archive', [MailBoxController::class, 'index'])->defaults('folder', 'archive')->name('archive');
        Route::get('/starred', [MailBoxController::class, 'index'])->defaults('folder', 'starred')->name('starred');
        
        // Email management
        Route::get('/mail/{id}', [MailBoxController::class, 'show'])->name('show');
        Route::get('/mail/{id}/reply', [MailBoxController::class, 'reply'])->name('reply');
        Route::post('/mail/{id}/reply', [MailBoxController::class, 'replyStore'])->name('reply.store');
        
        // Compose & Send
        Route::get('/compose', [MailBoxController::class, 'compose'])->name('compose');
        Route::post('/send', [MailBoxController::class, 'send'])->name('send');
        
        // Email actions
        Route::post('/action', [MailBoxController::class, 'action'])->name('action');
        

    });
});

