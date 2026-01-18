<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    private static $productCount = 1;

    public function definition(): array
    {
        $mrp      = $this->faker->numberBetween(200, 3000);
        $discount = rand(10, 20);

        // Introduce a variable to decide whether to include offer_price
        $includeOfferPrice = $this->faker->boolean(50);

        $offerPrice    = $includeOfferPrice ? ($mrp - (($mrp * $discount) / 100)) : 0;
        $offer_percent = $includeOfferPrice ? (($mrp - $offerPrice) / $mrp) * 100 : 0;

        $image         = $this->faker->numberBetween(1, 14) . ".png";

        // Generate sequential product names
        $name = "Product " . self::$productCount++;

        return [
            'name'              => $name,
            'slug'              => Str::slug($name),
            'description'       => $this->faker->words(rand(80, 300), true),
            'short_description' => $this->faker->words(rand(10, 100), true),
            'brand_id'          => rand(1, 5),
            'category_id'       => rand(1, 5),
              // 'sub_category_id'  => rand(1, 5),
            'buy_price'          => 150,
            'mrp'                => $mrp,
            'offer_price'        => $offerPrice,
            'sell_price'         => $offerPrice > 0 ? $offerPrice : $mrp,
            'discount'           => $includeOfferPrice ? $discount : 0,
            'offer_percent'      => $offer_percent,
            'total_purchase_qty' => 100,
            'current_stock'      => 100,
            'status'             => StatusEnum::ACTIVE,
            'img_path'           => "uploads/products/" . $image,
        ];
    }
}
