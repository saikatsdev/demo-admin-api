<?php

namespace Database\Seeders\Product;

use Illuminate\Database\Seeder;
use App\Models\Product\AttributeValue;

class AttributeValueSeeder extends Seeder
{
    public function run()
    {
        AttributeValue::insert([
            [
                "id"           => 1,
                "attribute_id" => 1,
                "value"        => "M",
                "slug"         => "m"
            ],
            [
                "id"           => 2,
                "attribute_id" => 1,
                "value"        => "L",
                "slug"         => "l"
            ],
            [
                "id"           => 3,
                "attribute_id" => 1,
                "value"        => "XL",
                "slug"         => "xl"
            ],
            [
                "id"           => 4,
                "attribute_id" => 1,
                "value"        => "XLL",
                "slug"         => "xll"
            ],
            [
                "id"           => 5,
                "attribute_id" => 2,
                "value"        => "Red",
                "slug"         => "red"
            ],
            [
                "id"           => 6,
                "attribute_id" => 2,
                "value"        => "Green",
                "slug"         => "green"
            ],
            [
                "id"           => 7,
                "attribute_id" => 2,
                "value"        => "Blue",
                "slug"         => "blue"
            ],
            [
                "id"           => 8,
                "attribute_id" => 3,
                "value"        => "1 KG",
                "slug"         => "1-kg"
            ],
            [
                "id"           => 9,
                "attribute_id" => 3,
                "value"        => "2 KG",
                "slug"         => "2-kg"
            ],
            [
                "id"           => 10,
                "attribute_id" => 3,
                "value"        => "3 KG",
                "slug"         => "3-kg"
            ],
        ]);
    }
}
