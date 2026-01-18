<?php

namespace Database\Seeders\Product;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Product\SubCategory;

class SubCategorySeeder extends Seeder
{
    public function run()
    {
        $status = StatusEnum::ACTIVE;

        SubCategory::insert([
            [
                "name"        => "Sub Category 1",
                "slug"        => "sub-category-1",
                "status"      => $status,
                "category_id" => 1,
            ],
            [
                "name"        => "Sub Category 2",
                "slug"        => "sub-category-2",
                "status"      => $status,
                "category_id" => 1,
            ],
            [
                "name"        => "Sub Category 3",
                "slug"        => "sub-category-3",
                "status"      => $status,
                "category_id" => 1,
            ],
            [
                "name"        => "Sub Category 4",
                "slug"        => "sub-category-4",
                "status"      => $status,
                "category_id" => 1,
            ],
            [
                "name"        => "Sub Category 5",
                "slug"        => "sub-category-5",
                "status"      => $status,
                "category_id" => 1,
            ],
        ]);
    }
}
