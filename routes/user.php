<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\UserCategoryController;

// Auth route
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Dashboard route
    Route::get('dashboard',   [DashboardController::class, 'dashboard']);
    Route::get('cache-clear', [DashboardController::class, 'cacheClear']);

    // Permission route
    Route::prefix('permissions')->group(function () {
        Route::controller(PermissionController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/list',    'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Role route
    Route::prefix('roles')->group(function () {
        Route::controller(RoleController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // User Category route
    Route::prefix('user-categories')->group(function () {
        Route::controller(UserCategoryController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // User route
    Route::prefix('users')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/permission',               'userPermission');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    Route::prefix("customers")->group(function () {
        Route::controller(CustomerController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/summary', 'getCustomerSummary');
        });
    });

    Route::post('logout', [AuthController::class, 'logout']);
});
