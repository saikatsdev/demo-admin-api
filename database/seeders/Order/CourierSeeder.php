<?php

namespace Database\Seeders\Order;

use App\Enums\StatusEnum;
use App\Models\Order\Courier;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    public function run()
    {
        $status = StatusEnum::ACTIVE;

        Courier::insert([
            [
                "id"     => 1,
                "name"   => "Steadfast",
                "slug"   => "steadfast",
                "status" => $status
            ],
            [
                "id"     => 2,
                "name"   => "Pathao",
                "slug"   => "pathao",
                "status" => $status
            ],
            [
                "id"     => 3,
                "name"   => "Redx",
                "slug"   => "redx",
                "status" => $status
            ],
        ]);
    }
}
