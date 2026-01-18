<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\SettingCategory;
use Illuminate\Database\Seeder;

class SettingCategorySeeder extends Seeder
{
    public function run(): void
    {
        SettingCategory::insert([
            [
                "id"     => 1,
                "name"   => "General",
                "slug"   => "general",
                "status" => StatusEnum::ACTIVE,
            ],
            [
                "id"     => 2,
                "name"   => "Logo",
                "slug"   => "logo",
                "status" => StatusEnum::ACTIVE,
            ],
            [
                "id"     => 3,
                "name"   => "Product",
                "slug"   => "product",
                "status" => StatusEnum::ACTIVE,
            ],
            [
                "id"     => 4,
                "name"   => "Frontend Header & Footer",
                "slug"   => "frontend-header-footer",
                "status" => StatusEnum::ACTIVE,
            ],
            [
                "id"     => 5,
                "name"   => "Top Header",
                "slug"   => "top-header",
                "status" => StatusEnum::ACTIVE,
            ],
            [
                "id"     => 6,
                "name"   => "Frontend Theme",
                "slug"   => "frontend-theme",
                "status" => StatusEnum::ACTIVE,
            ],
            [
                "id"     => 7,
                "name"   => "Checkout",
                "slug"   => "checkout",
                "status" => StatusEnum::ACTIVE,
            ],
        ]);
    }
}
