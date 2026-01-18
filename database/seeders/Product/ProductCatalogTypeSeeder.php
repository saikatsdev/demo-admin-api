<?php

namespace Database\Seeders\Product;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Product\ProductCatalogType;

class ProductCatalogTypeSeeder extends Seeder
{
    public function run()
    {
        ProductCatalogType::insert([
            [
                "name" => "Facebook Product Catalog",
                "slug" => "facebook-product-catalog",
                "status" => StatusEnum::ACTIVE
            ]
        ]);
    }
}
