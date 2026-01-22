<?php

namespace Database\Seeders\Product;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Product\SubSubCategory;

class SubSubCategorySeeder extends Seeder
{
    public function run(): void
    {
        $status = StatusEnum::ACTIVE;

        SubSubCategory::upsert([
            ['name' => 'Gaming Laptops',    'slug' => 'gaming-laptops',    'status' => $status, 'sub_category_id' => 1],
            ['name' => 'Business Laptops',  'slug' => 'business-laptops',  'status' => $status, 'sub_category_id' => 1],

            ['name' => 'LED TVs',           'slug' => 'led-tvs',           'status' => $status, 'sub_category_id' => 2],
            ['name' => 'Smart TVs',         'slug' => 'smart-tvs',         'status' => $status, 'sub_category_id' => 2],

            ['name' => 'Android Phones',    'slug' => 'android-phones',    'status' => $status, 'sub_category_id' => 4],
            ['name' => 'iPhones',           'slug' => 'iphones',           'status' => $status, 'sub_category_id' => 4],

            ['name' => 'T-Shirts',          'slug' => 't-shirts',          'status' => $status, 'sub_category_id' => 7],
            ['name' => 'Jeans',             'slug' => 'jeans',             'status' => $status, 'sub_category_id' => 7],

            ['name' => 'Full Face Helmets', 'slug' => 'full-face-helmets', 'status' => $status, 'sub_category_id' => 10],
            ['name' => 'Open Face Helmets', 'slug' => 'open-face-helmets', 'status' => $status, 'sub_category_id' => 10],

            ['name' => 'Single Door',       'slug' => 'single-door',       'status' => $status, 'sub_category_id' => 13],
            ['name' => 'Double Door',       'slug' => 'double-door',       'status' => $status, 'sub_category_id' => 13],
        ], ['slug'], ['name', 'status', 'sub_category_id']);
    }
}
