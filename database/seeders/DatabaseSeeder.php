<?php

namespace Database\Seeders;

use Database\Seeders\TagSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\LaratrustSeeder;
use Database\Seeders\Order\PathaoSeeder;
use Database\Seeders\Order\StatusSeeder;
use Database\Seeders\UserCategorySeeder;
use Database\Seeders\Order\CourierSeeder;
use Database\Seeders\Product\BrandSeeder;
use Database\Seeders\Order\DistrictSeeder;
use Database\Seeders\Order\OrderFromSeeder;
use Database\Seeders\Product\ProductSeeder;
use Database\Seeders\Product\SectionSeeder;
use Database\Seeders\SettingCategorySeeder;
use Database\Seeders\Order\OrderGuardSeeder;
use Database\Seeders\Product\CategorySeeder;
use Database\Seeders\Product\AttributeSeeder;
use Database\Seeders\Order\CancelReasonSeeder;
use Database\Seeders\Order\CustomerTypeSeeder;
use Database\Seeders\Product\ProductTypeSeeder;
use Database\Seeders\Product\SubCategorySeeder;
use Database\Seeders\Order\PaymentGatewaySeeder;
use Database\Seeders\Order\DeliveryGatewaySeeder;
use Database\Seeders\Order\OrderPermissionSeeder;
use Database\Seeders\Product\AttributeValueSeeder;
use Database\Seeders\Product\ProductVariationSeeder;
use Database\Seeders\Product\ProductPermissionSeeder;
use Database\Seeders\Product\ProductCatalogTypeSeeder;
use Database\Seeders\Order\OnlinePaymentDiscountSeeder;
use Database\Seeders\Product\SubSubCategorySeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $baseSeeders = [
            LaratrustSeeder::class,
            UserCategorySeeder::class,
            AdminSeeder::class,
            SettingCategorySeeder::class,
            SettingSeeder::class,
            TagSeeder::class,
            OrderFromSeeder::class,
            StatusSeeder::class,
            CustomerTypeSeeder::class,
            PathaoSeeder::class,

            CMSPermissionSeeder::class,
            BlogPostPermissionSeeder::class,

            ProductTypeSeeder::class,
            ProductCatalogTypeSeeder::class,
            ProductPermissionSeeder::class,
            OrderPermissionSeeder::class,
        ];

        $devSeeders = [
            AttributeSeeder::class,
            AttributeValueSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            SubCategorySeeder::class,
            SubSubCategorySeeder::class,
            ProductSeeder::class,
            ProductVariationSeeder::class,
            SectionSeeder::class,

            // order
            CancelReasonSeeder::class,
            CourierSeeder::class,
            DeliveryGatewaySeeder::class,
            DistrictSeeder::class,
            PaymentGatewaySeeder::class,
            OnlinePaymentDiscountSeeder::class,
            OrderGuardSeeder::class
        ];

        $seeders = config('app.app_mode') === 'development' ? array_merge($baseSeeders, $devSeeders) : $baseSeeders;

        $this->call($seeders);
    }
}
