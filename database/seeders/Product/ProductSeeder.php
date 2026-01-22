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
        $products = [
            'Samsung Galaxy S23',
            'Apple iPhone 14',
            'Nike Air Max Sneakers',
            'Honda CB Shine Bike',
            'Philips Air Fryer',
            'Sony Bravia LED TV',
            'LG Refrigerator 260L',
            'Dell Inspiron Laptop',
            'Adidas Sports T-Shirt',
            'BMW G310R Motorcycle'
        ];

        foreach ($products as $productName) {
            $mrp      = rand(500, 1000);
            $discount = rand(10, 20);

            $includeOfferPrice = (bool) rand(0, 1);
            $offerPrice    = $includeOfferPrice ? ($mrp - (($mrp * $discount) / 100)) : 0;
            $offer_percent = $includeOfferPrice ? (($mrp - $offerPrice) / $mrp) * 100 : 0;

            Product::create([
                'name'              => $productName,
                'slug'              => Str::slug($productName),
                'brand_id'          => rand(1, 5),
                'category_id'       => rand(1, 5),
                'buy_price'         => 400,
                'mrp'               => $mrp,
                'offer_price'       => $offerPrice,
                'sell_price'        => $offerPrice > 0 ? $offerPrice : $mrp,
                'discount'          => $includeOfferPrice ? $discount : 0,
                'offer_percent'     => $offer_percent,
                'total_purchase_qty'=> 100,
                'current_stock'     => 100,
                'status'            => StatusEnum::ACTIVE,
                'img_path'          => "uploads/products/" . rand(1, 14) . ".png",
            ]);
        }
    }
}
