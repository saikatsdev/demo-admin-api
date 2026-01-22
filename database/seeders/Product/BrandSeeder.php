<?php

namespace Database\Seeders\Product;

use App\Enums\StatusEnum;
use App\Models\Product\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $status = StatusEnum::ACTIVE;

        Brand::upsert([
            [
                'name'   => 'Samsung',
                'slug'   => 'samsung',
                'status' => $status,
            ],
            [
                'name'   => 'Apple',
                'slug'   => 'apple',
                'status' => $status,
            ],
            [
                'name'   => 'Nike',
                'slug'   => 'nike',
                'status' => $status,
            ],
            [
                'name'   => 'Honda',
                'slug'   => 'honda',
                'status' => $status,
            ],
            [
                'name'   => 'Philips',
                'slug'   => 'philips',
                'status' => $status,
            ],
        ], ['slug'], ['name', 'status']);
    }
}
