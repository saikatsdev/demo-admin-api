<?php

namespace Database\Seeders\Product;

use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $mrp      = rand(500, 1000);
        $discount = rand(10, 20);

        // Introduce a variable to decide whether to include offer_price
        $includeOfferPrice = (bool) rand(0, 1);;

        $offerPrice    = $includeOfferPrice ? ($mrp - (($mrp * $discount) / 100)) : 0;
        $offer_percent = $includeOfferPrice ? (($mrp - $offerPrice) / $mrp) * 100 : 0;

        for ($i = 1; $i <= 10; $i++) {
            $productName = "Product $i";

            Product::create([
                'name'              => $productName,
                'slug'              => Str::slug($productName),
                'brand_id'          => rand(1, 5),
                'category_id'       => rand(1, 5),
                'buy_price'          => 400,
                'mrp'                => $mrp,
                'offer_price'        => $offerPrice,
                'sell_price'         => $offerPrice > 0 ? $offerPrice : $mrp,
                'discount'           => $includeOfferPrice ? $discount : 0,
                'offer_percent'      => $offer_percent,
                'total_purchase_qty' => 100,
                'current_stock'      => 100,
                'status'             => StatusEnum::ACTIVE,
                'img_path'           => "uploads/products/" . rand(1, 14) . ".png",
            ]);
        }
    }
}
