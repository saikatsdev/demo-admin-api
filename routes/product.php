<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Product\BrandController;
use App\Http\Controllers\Backend\Product\ReviewController;
use App\Http\Controllers\Backend\Product\UpSellController;
use App\Http\Controllers\Backend\Product\GalleryController;
use App\Http\Controllers\Backend\Product\ProductController;
use App\Http\Controllers\Backend\Product\SectionController;
use App\Http\Controllers\Backend\Product\CampaignController;
use App\Http\Controllers\Backend\Product\CategoryController;
use App\Http\Controllers\Backend\Product\WarrantyController;
use App\Http\Controllers\Backend\Product\AttributeController;
use App\Http\Controllers\Backend\Product\ProductTypeController;
use App\Http\Controllers\Backend\Product\ReviewReplyController;
use App\Http\Controllers\Backend\Product\SubCategoryController;
use App\Http\Controllers\Backend\Product\ProductReviewController;
use App\Http\Controllers\Backend\Product\AttributeValueController;
use App\Http\Controllers\Backend\Product\ProductCatalogController;
use App\Http\Controllers\Backend\Product\SubSubCategoryController;
use App\Http\Controllers\Backend\Product\CategorySectionController;
use App\Http\Controllers\Backend\Product\UpSellSettingController;

Route::middleware(['auth:sanctum'])->group(function () {

    // Brand route
    Route::prefix('brands')->group(function () {
        Route::controller(BrandController::class)->group(function () {
            Route::get('/',                         'index');
            Route::get('/list',                     'list');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Category route
    Route::prefix('categories')->group(function () {
        Route::controller(CategoryController::class)->group(function () {
            Route::get('/',                         'index');
            Route::get('/list',                     'list');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::post('/update/categories/{id}',  'updateCategorySlug');
        Route::get('/check/categories',  'checkCategorySlug');
    });

    // Sub Category route
    Route::prefix('sub-categories')->group(function () {
        Route::controller(SubCategoryController::class)->group(function () {
            Route::get('/',                         'index');
            Route::get('/list',                     'list');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            // Route::get('/{id}',                     'getSubCategoryIdByCategoryId');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    Route::controller(SubCategoryController::class)->group(function () {
        Route::post('/subcategory/update/{id}',  'updateSubCategorySlug');
        Route::get('/subcategory/check',  'checkSubCategorySlug');
    });

    // Sub Sub Category route
    Route::prefix('sub-sub-categories')->group(function () {
        Route::controller(SubSubCategoryController::class)->group(function () {
            Route::get('/',                         'index');
            Route::get('/list',                     'list');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            // Route::get('/{id}',                     'getSubSubCategoryIdBySubCategoryId');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Product type route
    Route::prefix('product-types')->group(function () {
        Route::controller(ProductTypeController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/list',    'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Attribute route
    Route::prefix('attributes')->group(function () {
        Route::controller(AttributeController::class)->group(function () {
            Route::get('/',                         'index');
            Route::get('/list',                     'list');
            Route::get('/trash',                    'trashList');
            Route::post('/',                        'store');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    Route::prefix('attribute-values')->group(function () {
        Route::controller(AttributeValueController::class)->group(function () {
            Route::get('/',                         'index');
            Route::get('/list',                     'list');
            Route::get('/trash',                    'trashList');
            Route::post('/',                        'store');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Product route
    Route::prefix('products')->group(function () {
        Route::controller(ProductController::class)->group(function () {
            Route::get('/',                             'index');
            Route::get('/list',                         'list');
            Route::get('/search',                       'search');
            Route::post('/',                            'store');
            Route::get('/trash',                        'trashList');
            Route::post('/bulk/update/status',          'bulkUpdateStatus');
            Route::delete('/bulk/delete',               'bulkDelete');
            Route::put('/bulk/restore',                 'bulkRestore');
            Route::delete('/bulk/permanent-delete',     'bulkPermanentDelete');
            Route::post('/copy/{id}',                   'copy');
            Route::get('/{id}',                         'show');
            Route::put('/{id}',                         'update');
            Route::delete('/{id}',                      'destroy');
            Route::get('/history/{id}',                 'productHistory');
            Route::put('/{id}/restore',                 'restore');
            Route::delete('/{id}/permanent-delete',     'permanentDelete');
            Route::get('/stock/report',                 'stockReport');
            Route::get('/variation/current-stock/{id}', 'variationCurrentStock');
        });
    });

    Route::controller(ProductController::class)->group(function () {
        Route::post('/update/product/{id}',  'updateProductSlug');
        Route::get('/check/product',  'checkProductSlug');
    });

    // Sections route
    Route::prefix('sections')->group(function () {
        Route::controller(SectionController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/list',    'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Category sections route
    Route::prefix('category-sections')->group(function () {
        Route::controller(CategorySectionController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Warranty route
    Route::prefix('warranties')->group(function () {
        Route::controller(WarrantyController::class)->group(function () {
            Route::get('/',           'index');
            Route::post('/',          'store');
            Route::get('/permission', 'userPermission');
            Route::get('/{id}',       'show');
            Route::put('/{id}',       'update');
            Route::delete('/{id}',    'destroy');
        });
    });

    // Product catalog route
    Route::prefix('product/catalogs')->group(function () {
        Route::controller(ProductCatalogController::class)->group(function () {
            Route::get('/',                         'index');
            Route::get('/trash',                    'trashList');
            Route::post('/generate-fb-xml-feed',    'generateFbXmlFeed');
            Route::get('/{id}',                     'show');
            Route::put('/update-fb-xml-feed/{id}',  'updateFbXmlFeed');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Up sell offer route
    Route::prefix('up-sells')->group(function () {
        Route::controller(UpSellController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::prefix('up-sell-settings')->group(function(){
        Route::controller(UpSellSettingController::class)->group(function(){
            Route::get('/', 'index');
            Route::put('/', 'update');
        });
    });

    // Campaign route
    Route::prefix('campaigns')->group(function () {
        Route::controller(CampaignController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Review route
    Route::prefix('reviews')->group(function () {
        Route::controller(ReviewController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/list',    'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::prefix('gallary')->group(function () {
        Route::controller(GalleryController::class)->group(function () {
            Route::get('/', 'index');
            Route::delete('/', 'destroy');
            Route::post('/restore', 'restore');
            Route::delete('/force-delete', 'forceDelete');
        });
    });

    Route::prefix('product/reviews')->group(function(){
        Route::controller(ProductReviewController::class)->group(function(){
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::post('/status/update', 'statusUpdate');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::prefix('reply/review')->group(function(){
        Route::controller(ReviewReplyController::class)->group(function(){
            Route::post('/', 'store');
        });
    });
});
