<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\SeoController;
use App\Http\Controllers\Backend\CMS\FAQController;
use App\Http\Controllers\Backend\CMS\AboutController;
use App\Http\Controllers\Backend\CMS\BannerController;
use App\Http\Controllers\Backend\CMS\SliderController;
use App\Http\Controllers\Backend\CMS\ContactController;
use App\Http\Controllers\Backend\CMS\PrivacyPolicyController;
use App\Http\Controllers\Backend\CMS\TermsAndConditionController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Slider route
    Route::prefix('sliders')->group(function () {
        Route::controller(SliderController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Banner route
    Route::prefix('banners')->group(function () {
        Route::controller(BannerController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // About route
    Route::prefix('abouts')->group(function () {
        Route::controller(AboutController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Contact route
    Route::prefix('contacts')->group(function () {
        Route::controller(ContactController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // FAQ route
    Route::prefix('faqs')->group(function () {
        Route::controller(FAQController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Privacy Policy route
    Route::prefix('privacy-policies')->group(function () {
        Route::controller(PrivacyPolicyController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Terms And Condition route
    Route::prefix('terms-and-conditions')->group(function () {
        Route::controller(TermsAndConditionController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::prefix('seo')->group(function(){
        Route::controller(SeoController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });
});
