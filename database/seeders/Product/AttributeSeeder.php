<?php

namespace Database\Seeders\Product;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Product\Attribute;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        $status = StatusEnum::ACTIVE;

        Attribute::insert([
            [
                "id"     => 1,
                "name"   => "Size",
                "slug"   => "size",
                "status" => $status
            ],
            [
                "id"     => 2,
                "name"   => "Color",
                "slug"   => "color",
                "status" => $status
            ],
            [
                "id"     => 3,
                "name"   => "Weight",
                "slug"   => "weight",
                "status" => $status
            ]
        ]);
    }
}
