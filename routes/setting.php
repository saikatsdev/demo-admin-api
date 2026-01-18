<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\PusherController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\MarketingToolController;
use App\Http\Controllers\Backend\SettingCategoryController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Setting Category route
    Route::prefix('setting-category')->group(function () {
        Route::controller(SettingCategoryController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Setting route
    Route::prefix('settings')->group(function () {
        Route::controller(SettingController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('list/',     'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');

            // Update module status
            Route::get('/get/module-status',    'getModuleStatus');
            Route::put('/update/module-status', 'updateModuleStatus');
        });
    });

    Route::prefix("/marketing-tools")->group(function () {
        Route::controller(MarketingToolController::class)->group(function () {
            Route::get("/", "index");
            Route::put("/gtm", "gtmStore");
            Route::put("/clarity", "clarityStore");
            Route::put("/pixel", "pixelStore");
            Route::put("/conversion", "conversionStore");
            Route::put("/analytical", "analyticalStore");
            Route::put("/event", "eventStore");
        });
    });
});

Route::prefix("pusher")->group(function(){
    Route::controller(PusherController::class)->group(function(){
        Route::get("/", "index");
        Route::put("/", "update");
    });
});
