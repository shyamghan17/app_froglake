<?php

use Illuminate\Support\Facades\Route;
use Workdo\EBilling\Http\Controllers\Api\EBillingItemApiController;

Route::prefix('api')->middleware(['api.json'])->group(function () {
    Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'ebilling'], function () {
        Route::get('items', [EBillingItemApiController::class, 'index']);
        Route::post('items', [EBillingItemApiController::class, 'store']);
        Route::put('items/{item}', [EBillingItemApiController::class, 'update']);
        Route::delete('items/{item}', [EBillingItemApiController::class, 'destroy']);
    });
});

