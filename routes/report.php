<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ReportController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Order report
    Route::prefix('order/reports')->group(function () {
        Route::controller(ReportController::class)->group(function () {
            Route::get('/',               'orderReport');
            Route::get('/monthly',        'orderReportMonthly');
            Route::get('/yearly',         'orderReportYearly');
            Route::get('/by-location',    'orderReportByLocation');
            Route::get('/by-selling',     'orderReportBySelling');
            Route::get('/by-customer',    'orderReportByCustomer');
            Route::get('/profit-summary', 'orderProfitSummaryReport');
            Route::get('/profit',         'orderProfitReport');
            Route::get('/return',         'orderReturnReport');
            Route::get('/cancel',         'orderCancelReport');
            Route::get('/down-sell',         'downsellReport');
            Route::get('/followup',         'followUpReport');
        });
    });

    // Report
    Route::controller(ReportController::class)->group(function () {
        Route::get('purchase/reports',   'purchaseReport');
        Route::get('supplier/reports',   'supplierReport');
        Route::get('expense/reports',    'expenseReport');
        Route::get('net-profit/reports', 'netProfitReport');
        Route::get("incomplete/order/reports", 'incompleteOrderReport');
        Route::get("lowest/stock/products", "getLowestProducts");
    });

    Route::controller(ReportController::class)->group(function () {
        Route::prefix('down-sell')->group(function(){
            Route::get("/reports", "getDownSellReport");
        });
    });
});
