<?php

namespace Database\Seeders\Order;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Order\DeliveryGateway;

class DeliveryGatewaySeeder extends Seeder
{
    public function run()
    {
        $now    = now();
        $status = StatusEnum::ACTIVE;

        DeliveryGateway::insert([
            [
                'name'         => "Dhaka",
                'slug'         => "dhaka",
                'delivery_fee' => 60,
                'min_time'     => 1,
                'max_time'     => 3,
                'time_unit'    => 'Days',
                'status'       => $status,
                'created_at'   => $now
            ],
            [
                'name'         => "Others",
                'slug'         => "others",
                'delivery_fee' => 120,
                'min_time'     => 1,
                'max_time'     => 3,
                'time_unit'    => 'Days',
                'status'       => $status,
                'created_at'   => $now
            ]
        ]);
    }
}
