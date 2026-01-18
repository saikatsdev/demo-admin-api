<?php

namespace Database\Seeders\Order;

use App\Enums\StatusEnum;
use App\Models\Order\OrderFrom;
use Illuminate\Database\Seeder;

class OrderFromSeeder extends Seeder
{
    public function run()
    {
        $status = StatusEnum::ACTIVE;
        $now    = now();

        OrderFrom::insert([
            [
                "id"         => 1,
                "name"       => "Website",
                "slug"       => "website",
                "status"     => $status,
                "created_at" => $now,
            ],
            [
                "id"         => 2,
                "name"       => "Manual",
                "slug"       => "manual",
                "status"     => $status,
                "created_at" => $now,
            ],
            [
                "id"         => 3,
                "name"       => "Incomplete",
                "slug"       => "incomplete",
                "status"     => $status,
                "created_at" => $now,
            ]
        ]);
    }
}
