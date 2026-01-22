<?php

namespace Database\Seeders\Product;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Product\SubCategory;

class SubCategorySeeder extends Seeder
{
    public function run(): void
    {
        $status = StatusEnum::ACTIVE;

        SubCategory::upsert([
            ['name' => 'Laptops',         'slug' => 'laptops',         'status' => $status, 'category_id' => 1],
            ['name' => 'Televisions',     'slug' => 'televisions',     'status' => $status, 'category_id' => 1],
            ['name' => 'Speakers',        'slug' => 'speakers',        'status' => $status, 'category_id' => 1],

            ['name' => 'Smartphones',     'slug' => 'smartphones',     'status' => $status, 'category_id' => 2],
            ['name' => 'Smartwatches',    'slug' => 'smartwatches',    'status' => $status, 'category_id' => 2],
            ['name' => 'Headphones',      'slug' => 'headphones',      'status' => $status, 'category_id' => 2],

            ['name' => 'Men Clothing',    'slug' => 'men-clothing',    'status' => $status, 'category_id' => 3],
            ['name' => 'Women Clothing',  'slug' => 'women-clothing',  'status' => $status, 'category_id' => 3],
            ['name' => 'Shoes',           'slug' => 'shoes',           'status' => $status, 'category_id' => 3],

            ['name' => 'Helmets',         'slug' => 'helmets',         'status' => $status, 'category_id' => 4],
            ['name' => 'Gloves',          'slug' => 'gloves',          'status' => $status, 'category_id' => 4],
            ['name' => 'Spare Parts',     'slug' => 'spare-parts',     'status' => $status, 'category_id' => 4],

            ['name' => 'Refrigerators',   'slug' => 'refrigerators',   'status' => $status, 'category_id' => 5],
            ['name' => 'Microwaves',      'slug' => 'microwaves',      'status' => $status, 'category_id' => 5],
            ['name' => 'Air Conditioners','slug' => 'air-conditioners','status' => $status, 'category_id' => 5],
        ], ['slug'], ['name', 'status', 'category_id']);
    }
}
