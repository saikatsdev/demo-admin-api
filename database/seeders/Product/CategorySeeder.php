<?php

namespace Database\Seeders\Product;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Product\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $status = StatusEnum::ACTIVE;

        Category::upsert([
            [
                'name'   => 'Electronics',
                'slug'   => 'electronics',
                'status' => $status,
            ],
            [
                'name'   => 'Mobile & Gadgets',
                'slug'   => 'mobile-gadgets',
                'status' => $status,
            ],
            [
                'name'   => 'Fashion & Fabric',
                'slug'   => 'fashion-fabric',
                'status' => $status,
            ],
            [
                'name'   => 'Bike Accessories',
                'slug'   => 'bike-accessories',
                'status' => $status,
            ],
            [
                'name'   => 'Home Appliances',
                'slug'   => 'home-appliances',
                'status' => $status,
            ],
        ], ['slug'], ['name', 'status']);
    }
}
