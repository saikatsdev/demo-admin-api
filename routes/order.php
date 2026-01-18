<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Order\RedxController;
use App\Http\Controllers\Backend\Order\OrderController;
use App\Http\Controllers\Backend\Order\CouponController;
use App\Http\Controllers\Backend\Order\PathaoController;
use App\Http\Controllers\Backend\Order\StatusController;
use App\Http\Controllers\Backend\Order\CourierController;
use App\Http\Controllers\Backend\Order\DistrictController;
use App\Http\Controllers\Backend\Order\DownSellController;
use App\Http\Controllers\Backend\Order\BlockUserController;
use App\Http\Controllers\Backend\Order\OrderFromController;
use App\Http\Controllers\Backend\Order\OrderLockController;
use App\Http\Controllers\Backend\Order\OrderNoteController;
use App\Http\Controllers\Backend\Order\SteadFastController;
use App\Http\Controllers\Backend\Order\OrderGuardController;
use App\Http\Controllers\Backend\Order\CancelReasonController;
use App\Http\Controllers\Backend\Order\CustomerTypeController;
use App\Http\Controllers\Backend\Order\FreeDeliveryController;
use App\Http\Controllers\Backend\Order\PaymentGatewayController;
use App\Http\Controllers\Backend\Order\ReturnOrDamageController;
use App\Http\Controllers\Backend\Order\DeliveryGatewayController;
use App\Http\Controllers\Backend\Order\IncompleteOrderController;
use App\Http\Controllers\Backend\Order\CourierDeliveryReportController;
use App\Http\Controllers\Backend\Order\FollowupController;
use App\Http\Controllers\Backend\Order\OnlinePaymentDiscountController;

Route::post('/stead-fast/callback', [SteadFastController::class, 'callback']);
Route::post('/pathao/callback',     [PathaoController::class, 'callback']);

Route::middleware(['auth:sanctum'])->group(function () {
    // District route
    Route::prefix('districts')->group(function () {
        Route::controller(DistrictController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/list',    'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // DeliveryGateway route
    Route::prefix('delivery-gateways')->group(function () {
        Route::controller(DeliveryGatewayController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/list',    'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::prefix('free-delivery')->group(function () {
        Route::controller(FreeDeliveryController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // PaymentGateway route
    Route::prefix('payment-gateways')->group(function () {
        Route::controller(PaymentGatewayController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/list',    'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Status route
    Route::prefix('statuses')->group(function () {
        Route::controller(StatusController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/list',    'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Customer type route
    Route::prefix('customer-types')->group(function () {
        Route::controller(CustomerTypeController::class)->group(function () {
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

    // Order From route
    Route::prefix('order-froms')->group(function () {
        Route::controller(OrderFromController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/list',    'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Cancel reason route
    Route::prefix('cancel-reasons')->group(function () {
        Route::controller(CancelReasonController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/list',    'list');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Coupon route
    Route::prefix('coupons')->group(function () {
        Route::controller(CouponController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Order note route
    Route::prefix('orders/notes')->group(function () {
        Route::controller(OrderNoteController::class)->group(function () {
            Route::get('/',        'orderNoteList');
            Route::post('/',       'orderNoteStore');
            Route::put('/{id}',    'orderNoteUpdate');
            Route::delete('/{id}', 'orderNoteDelete');
        });
    });

    // Order lock route
    Route::prefix('orders')->group(function () {
        Route::controller(OrderLockController::class)->group(function () {
            Route::get("/locked-status/{id}", 'orderLockedStatus');
            Route::post("/locked/{id}",       'orderLocked');
            Route::post("/unlocked/{id}",     'orderUnlocked');
        });
    });

    // Order route
    Route::prefix('orders')->group(function () {
        Route::controller(OrderController::class)->group(function () {
            Route::get('/',                         'index');
            Route::get('/list',                     'list');
            Route::get('/trash',                    'trashList');
            Route::post('/',                        'store');
            Route::get('/district-wise-count',      'districtWiseOrderCount');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::get('/multiple/invoice',         'multipleInvoice');
            Route::get('/history/{id}',             'orderHistory');
            Route::delete('/{id}',                  'destroy');
            Route::post('/update-status',           'updateStatus');
            Route::post('/send-courier',           'sendCourier');
            Route::post('/update-paid-status',      'updatePaidStatus');
            Route::post('/add-additional-cost',     'addAdditionCost');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
            Route::get('/search-by/phone-number',   'searchByPhoneNumber');
            Route::get('/piking/list',              'pikingList');
            Route::get('/item/list',                'itemList');

            Route::put('/invoice/update/{id}', 'updateInvoice');
        });
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/latest/orders',  'latestOrders');
    });

    Route::prefix('incomplete-orders')->group(function () {
        Route::controller(IncompleteOrderController::class)->group(function () {
            Route::get('/',        'index');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::prefix('incomplete/orders')->group(function () {
        Route::controller(IncompleteOrderController::class)->group(function () {
            Route::get('/trashed', 'trashed');
            Route::post('/restore/{id}', 'restore');
        });
    });

    Route::prefix('down-sells')->group(function () {
        Route::controller(DownSellController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::prefix('online-payment/discounts')->group(function () {
        Route::controller(OnlinePaymentDiscountController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Return and damage route
    Route::prefix('order/return-or-damages')->group(function () {
        Route::controller(ReturnOrDamageController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Block user route
    Route::prefix('block-users')->group(function () {
        Route::controller(BlockUserController::class)->group(function () {
            Route::get('/',     'index');
            Route::post('/',     'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::post('/by-phone',     'userBlock');
        });
    });

    // Order Guard route
    Route::prefix('order-guards')->group(function () {
        Route::controller(OrderGuardController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // ================================================= Courier =================================================
    // Courier route
    Route::prefix('couriers')->group(function () {
        Route::controller(CourierController::class)->group(function () {
            Route::get('/',                         'index');
            Route::get('/list',                     'list');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
            
            Route::post('/settings', 'courierSettings');
        });
    });

    // Stead fast route
    Route::prefix('stead-fasts')->group(function () {
        Route::controller(SteadFastController::class)->group(function () {
            Route::get('/show',                      'show');
            Route::post("/create-order",             'createOrder');
            Route::post("/bulk/create-order",        'bulkCreate');
            Route::get("/delivery-status/{orderId}", 'getDeliveryStatus');
            Route::get("/current-balance",           'getCurrentBalance');
            Route::post('/update/env-credential',    'updateEnvCredential');
        });
    });

    // Pathao route
    Route::prefix('pathao')->group(function () {
        Route::controller(PathaoController::class)->group(function () {
            Route::get('/show',                              'show');
            Route::get('/cities',                            'getCity');
            Route::get('/zones/{cityId}',                    'getZone');
            Route::get('/areas/{zoneId}',                    'getArea');
            Route::get('/stores',                            'getStore');
            Route::post('/stores',                           'createStore');
            Route::get('/orders/short-info/{consignmentId}', 'orderShortInfo');
            Route::post('/orders/create',                    'createOrder');
            Route::post('/orders/bulk/create',               'createBulkOrder');
            Route::post('/cost/calculate',                   'costCalculation');
            Route::post('/update/env-credential',            'updateEnvCredential');
            Route::post('/update/env-credential',            'updateEnvCredential');
            Route::get('/search-areas',                      'searchArea');
        });
    });

    // Redx route
    Route::prefix('redx')->group(function () {
        Route::controller(RedxController::class)->group(function () {
            Route::get('/show',                       'show');
            Route::get('/areas',                      'getArea');
            Route::post('/pickup-stores',             'createPickupStore');
            Route::get('/pickup-stores',              'getPickupStore');
            Route::get('/pickup-stores/details/{id}', 'getPickupStoreDetail');
            Route::get('/orders/track/{id}',          'parcelTrack');
            Route::post('/orders/parcel',             'parcelCreate');
            Route::get('/parcel/track/{parcelId}',    'parcelDetail');
            Route::post('/update/env-credential',     'updateEnvCredential');
        });
    });

    Route::prefix('followup')->group(function() {
        Route::controller(FollowupController::class)->group(function(){
            Route::get('/', 'index');
        });
    });
});

Route::controller(CourierDeliveryReportController::class)->group(function () {
    Route::get('/orders/courier/delivery/report', 'courierDeliveryReport');
});