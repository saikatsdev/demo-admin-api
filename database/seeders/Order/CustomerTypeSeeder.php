<?php

namespace Database\Seeders\Order;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Order\CustomerType;

class CustomerTypeSeeder extends Seeder
{
    public function run()
    {
        $status = StatusEnum::ACTIVE;

        CustomerType::insert([
            [
                "id"     => 1,
                "name"   => "New",
                "slug"   => "new",
                "status" => $status,
            ],
            [
                "id"     => 2,
                "name"   => "Regular",
                "slug"   => "regular",
                "status" => $status,
            ],
            [
                "id"     => 3,
                "name"   => "VIP",
                "slug"   => "vip",
                "status" => $status,
            ]
        ]);
    }
}
