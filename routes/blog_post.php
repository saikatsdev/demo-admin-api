<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\BlogPost\TagController;
use App\Http\Controllers\Backend\BlogPost\BlogPostController;
use App\Http\Controllers\Backend\BlogPost\BlogPostCategoryController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Blog post categories route
    Route::prefix('blog-post-categories')->group(function () {
        Route::controller(BlogPostCategoryController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');

            Route::get('/check', 'checkCategorySlug');
        });
    });

    Route::controller(BlogPostCategoryController::class)->group(function () {
        Route::post('/slug/category/update/{id}', 'updateCategorySlug');
        Route::get('/category/check', 'checkCategorySlug');
    });

    Route::prefix('tags')->group(function () {
        Route::controller(TagController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/list',    'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Blog post route
    Route::prefix('blog-posts')->group(function () {
        Route::controller(BlogPostController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::controller(BlogPostController::class)->group(function () {
        Route::post('/update/blog/{id}',  'updateBlogSlug');
        Route::get('/check/blog',  'checkBlogSlug');
    });
});
