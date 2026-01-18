<?php

namespace Database\Seeders\Product;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Product\ProductType;

class ProductTypeSeeder extends Seeder
{
    public function run()
    {
        $status = StatusEnum::ACTIVE;
        $now    = now();

        ProductType::insert([
            [
                "name"       => "Top Product",
                "slug"       => "top-product",
                "status"     => $status,
                "created_at" => $now
            ],
            [
                "name"       => "Feature Product",
                "slug"       => "feature-product",
                "status"     => $status,
                "created_at" => $now
            ],
            [
                "name"       => "New Product",
                "slug"       => "new-product",
                "status"     => $status,
                "created_at" => $now
            ],
            [
                "name"       => "Landing Page Product",
                "slug"       => "landing-page-product",
                "status"     => $status,
                "created_at" => $now
            ],
        ]);
    }
}
