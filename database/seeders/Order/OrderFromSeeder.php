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
                "name"       => "Frontend",
                "slug"       => "frontend",
                "status"     => $status,
                "created_at" => $now,
            ],
            [
                "id"         => 2,
                "name"       => "Adminend",
                "slug"       => "adminend",
                "status"     => $status,
                "created_at" => $now,
            ],
            [
                "id"         => 3,
                "name"       => "Landing Page",
                "slug"       => "landing-page",
                "status"     => $status,
                "created_at" => $now,
            ]
        ]);
    }
}
