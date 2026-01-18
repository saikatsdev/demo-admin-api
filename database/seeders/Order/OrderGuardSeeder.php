<?php

namespace Database\Seeders\Order;

use Illuminate\Database\Seeder;
use App\Models\Order\OrderGuard;

class OrderGuardSeeder extends Seeder
{
    public function run()
    {
        OrderGuard::insert([
            [
                "duration_type" => "minutes",
                "quantity"      => 5,
                "duration"      => 5,
                "status"        => "active"
            ]
        ]);
    }
}
