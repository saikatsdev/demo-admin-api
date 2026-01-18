<?php

namespace Database\Seeders\Product;

use App\Enums\StatusEnum;
use App\Models\Product\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $status = StatusEnum::ACTIVE;

        Brand::insert([
            [
                "name"   => "Brand 1",
                "slug"   => "brand-1",
                "status" => $status,
            ],
            [
                "name"   => "Brand 2",
                "slug"   => "brand-2",
                "status" => $status,
            ],
            [
                "name"   => "Brand 3",
                "slug"   => "brand-3",
                "status" => $status,
            ],
            [
                "name"   => "Brand 4",
                "slug"   => "brand-4",
                "status" => $status,
            ],
            [
                "name"   => "Brand 5",
                "slug"   => "brand-5",
                "status" => $status,
            ],
        ]);
    }
}
