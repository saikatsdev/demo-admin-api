<?php

namespace Database\Seeders\Order;

use Illuminate\Database\Seeder;
use App\Models\Order\CancelReason;

class CancelReasonSeeder extends Seeder
{
    public function run()
    {
        CancelReason::insert([
            [
                "id"         => 1,
                "name"       => "High price",
                "slug"       => "high-price",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id"         => 2,
                "name"       => "Short time delivery",
                "slug"       => "short-time-delivery",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id"         => 3,
                "name"       => "Long time delivery",
                "slug"       => "long-time-delivery",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id"         => 4,
                "name"       => "Duplicate order",
                "slug"       => "duplicate-order",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id"         => 5,
                "name"       => "Fake order",
                "slug"       => "fake-order",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id"         => 6,
                "name"       => "Others",
                "slug"       => "others",
                "created_at" => now(),
                "updated_at" => now()
            ],
        ]);
    }
}
