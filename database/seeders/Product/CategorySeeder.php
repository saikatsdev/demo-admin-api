<?php

namespace Database\Seeders\Product;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Product\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $status = StatusEnum::ACTIVE;

        Category::insert([
            [
                "name"   => "Category 1",
                "slug"   => "category-1",
                "status" => $status,
            ],
            [
                "name"   => "Category 2",
                "slug"   => "category-2",
                "status" => $status,
            ],
            [
                "name"   => "Category 3",
                "slug"   => "category-3",
                "status" => $status,
            ],
            [
                "name"   => "Category 4",
                "slug"   => "category-4",
                "status" => $status,
            ],
            [
                "name"   => "Category 5",
                "slug"   => "category-5",
                "status" => $status,
            ],
        ]);
    }
}
