<?php

use App\Helpers\Helper;
use App\Http\Controllers\Frontend\CMS\OrderPolicyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\ReportController;
use App\Http\Controllers\Frontend\CMS\FAQController;
use App\Http\Controllers\Frontend\SettingController;
use App\Http\Controllers\Frontend\CMS\AboutController;
use App\Http\Controllers\Frontend\CMS\BannerController;
use App\Http\Controllers\Frontend\CMS\SliderController;
use App\Http\Controllers\Frontend\CMS\ContactController;
use App\Http\Controllers\Frontend\Order\OrderController;
use App\Http\Controllers\Frontend\Order\CouponController;
use App\Http\Controllers\Frontend\Product\BrandController;
use App\Http\Controllers\Frontend\MarketingToolController;
use App\Http\Controllers\Frontend\Product\ProductController;
use App\Http\Controllers\Frontend\Product\SectionController;
use App\Http\Controllers\Frontend\Order\OrderGuardController;
use App\Http\Controllers\Frontend\Product\CampaignController;
use App\Http\Controllers\Frontend\Product\CategoryController;
use App\Http\Controllers\Frontend\BlogPost\BlogPostController;
use App\Http\Controllers\Frontend\CMS\PrivacyPolicyController;
use App\Http\Controllers\Frontend\Product\AttributeController;
use App\Http\Controllers\Frontend\Order\FreeDeliveryController;
use App\Http\Controllers\Frontend\Product\ProductTypeController;
use App\Http\Controllers\Frontend\Order\PaymentGatewayController;
use App\Http\Controllers\Frontend\Order\DeliveryGatewayController;
use App\Http\Controllers\Frontend\Order\IncompleteOrderController;
use App\Http\Controllers\Frontend\Product\ProductCatalogController;
use App\Http\Controllers\Frontend\Product\CategorySectionController;
use App\Http\Controllers\Frontend\BlogPost\BlogPostCategoryController;
use App\Http\Controllers\Frontend\CMS\MissionController;
use App\Http\Controllers\Frontend\CMS\ShippingPolicyController;
use App\Http\Controllers\Frontend\CMS\TermsConditionController;
use App\Http\Controllers\Frontend\CMS\ReturnRefundController;
use App\Http\Controllers\Frontend\CMS\WarrantyPolicyController;
use App\Http\Controllers\Frontend\Order\OnlinePaymentDiscountController;
use App\Http\Controllers\Frontend\Order\OrderTrackController;
use App\Http\Controllers\Frontend\Product\UpSellSettingController;
use App\Http\Controllers\Frontend\Product\ProductReviewController;

// Auth route
Route::prefix('users')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register',           'register');
        Route::post('/verification-otp',   'verificationOtp');
        Route::post('/resend-otp',         'resendOtp');
        Route::post('/reset-password-otp', 'resetPasswordOtp');
        Route::post('/reset-password',     'resetPassword');
        Route::post('/login',              'login');
    });

    Route::middleware(['auth:sanctum'])->controller(AuthController::class)->group(function () {
        Route::put('/change-password', 'changePassword');
        Route::put('/update-profile',  'updateProfile');
        Route::get('/get',             'userGet');
        Route::post('/logout',         'logout');
    });
});

// ================================================= Start blog post ================================================

// Blog Post Category route
Route::prefix('blog-post-categories')->group(function () {
    Route::controller(BlogPostCategoryController::class)->group(function () {
        Route::get('/',       'index');
        Route::get('/{slug}', 'show');
    });
});

// Blog Post route
Route::prefix('blog-posts')->group(function () {
    Route::controller(BlogPostController::class)->group(function () {
        Route::get('/',       'index');
        Route::get('/{slug}', 'show');
    });
});

// ================================================= End blog post =================================================


// ================================================= Start Product =================================================

// Brand route
Route::prefix('brands')->group(function () {
    Route::controller(BrandController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Category route
Route::prefix('categories')->group(function () {
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Attribute route
Route::prefix('attributes')->group(function () {
    Route::controller(AttributeController::class)->group(function () {
        Route::get('/', 'index');
    });
});

// product type route
Route::prefix('product-types')->group(function () {
    Route::controller(ProductTypeController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Product route
Route::prefix('products')->group(function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('/',                    'index');
        Route::get('/list',                'list');
        Route::get('/search',              'search');
        Route::get('/variations',          'productVariation');
        Route::get('/shop-sidebar',        'shopSidebarData');
        Route::get('/{slug}',              'show')->name('products.show');
        Route::get('/category/{slug}',     'categoryWiseProduct');
        Route::get('/sub-category/{slug}', 'subCategoryWiseProduct');
    });
});

// Section route
Route::prefix('sections')->group(function () {
    Route::controller(SectionController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Category section route
Route::prefix('category-sections')->group(function () {
    Route::controller(CategorySectionController::class)->group(function () {
        Route::get('/', 'index');
    });
});

// Product catalog route
Route::prefix('product/catalogs')->group(function () {
    Route::controller(ProductCatalogController::class)->group(function () {
        Route::get('/{slug}', 'getFbXmlProductCatalog');
    });
});

Route::prefix('up-sell-settings')->group(function () {
    Route::controller(UpSellSettingController::class)->group(function () {
        Route::get('/', 'index');
    });
});

// Campaign route
Route::prefix('campaigns')->group(function () {
    Route::controller(CampaignController::class)->group(function () {
        Route::get('/',         'index');
        Route::get('/{id}',     'show');
        Route::get('/products', 'campaignProductPrice');
        Route::get('/products/{camSlug}/{prodSlug}', 'campaignProductDetail');
    });
});

// ================================================= End Product =================================================


// ================================================= Start Order =================================================

// Delivery gateway route
Route::prefix('delivery-gateway')->group(function () {
    Route::controller(DeliveryGatewayController::class)->group(function () {
        Route::get('/',           'index');
        Route::get('/{id}',       'show');
        Route::get('/price/{id}', 'deliveryPrice');
    });
});

// Free Delivery route
Route::prefix('free-delivery')->group(function () {
    Route::controller(FreeDeliveryController::class)->group(function () {
        Route::get('/get', 'getFreeDelivery');
    });
});

// Payment gateway
Route::prefix('payment-gateway')->group(function () {
    Route::controller(PaymentGatewayController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Order Guard route
Route::prefix('order-guards')->group(function () {
    Route::controller(OrderGuardController::class)->group(function () {
        Route::get('/', 'get');
    });
});


// Coupon route
Route::prefix('coupons')->group(function () {
    Route::controller(CouponController::class)->group(function () {
        Route::get('/',     'getCoupon');
        Route::get('/check', 'checkCouponCode');
    });
});


Route::prefix('incomplete-orders')->group(function () {
    Route::controller(IncompleteOrderController::class)->group(function () {
        Route::post('/', 'store');
    });
});

// Online payment discount
Route::prefix('online-payment/discounts')->group(function () {
    Route::controller(OnlinePaymentDiscountController::class)->group(function () {
        Route::get('/', 'index');
    });
});


$middleware = "auth.optional"; // For optional login

if (Schema::hasTable('settings') && Helper::setting("is_login_required")) {
    $middleware = "auth:sanctum";
}

Route::middleware([$middleware])->group(function () {
    Route::prefix('orders')->group(function () {
        Route::controller(OrderController::class)->group(function () {
            Route::get('/',                'index');
            Route::post('/',               'store');
            Route::post('/up-sell',      'upSellOrder');
            Route::get('/{id}',            'show');
        });
    });
});

// ================================================= End Order =================================================

// ================================================= Start CMS =================================================

// Slider route
Route::prefix('sliders')->group(function () {
    Route::controller(SliderController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Banner route
Route::prefix('banners')->group(function () {
    Route::controller(BannerController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});


// About route
Route::prefix('abouts')->group(function () {
    Route::controller(AboutController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Contact route
Route::prefix('contacts')->group(function () {
    Route::controller(ContactController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
    });
});

// Privacy Policy
Route::prefix('privacy-policies')->group(function () {
    Route::controller(PrivacyPolicyController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

Route::prefix('shipping-policies')->group(function () {
    Route::controller(ShippingPolicyController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

Route::prefix('warranty-policies')->group(function () {
    Route::controller(WarrantyPolicyController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

Route::prefix('order-policies')->group(function () {
    Route::controller(OrderPolicyController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

Route::prefix('refund-policies')->group(function () {
    Route::controller(ReturnRefundController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

Route::prefix('missions')->group(function () {
    Route::controller(MissionController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Faq route
Route::prefix('faqs')->group(function () {
    Route::controller(FAQController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// ================================================= End CMS =================================================

// Setting route
Route::controller(SettingController::class)->group(function () {
    Route::get('/settings',     'index');
    Route::get('/settings/{id}', 'show');
    Route::get('/is-module-active', 'isModuleActive');
});

Route::controller(ReportController::class)->group(function () {
    Route::get("/top/selling/products", 'topSelling');
});

Route::controller(MarketingToolController::class)->group(function () {
    Route::get("/all/tools", 'index');
});

Route::prefix("/terms/condition")->group(function(){
    Route::controller(TermsConditionController::class)->group(function(){
        Route::get("/", "index");
    });
});

Route::prefix("review")->group(function(){
    Route::controller(ProductReviewController::class)->group(function(){
        Route::post("/", 'store');
    });
});

Route::prefix("order/track")->group(function(){
    Route::controller(OrderTrackController::class)->group(function(){
        Route::get("/", "index");
    });
});
