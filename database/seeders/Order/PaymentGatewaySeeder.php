<?php

namespace Database\Seeders\Order;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Order\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    public function run()
    {
        $now    = now();
        $status = StatusEnum::ACTIVE;

        PaymentGateway::insert([
            [
                'id'           => 1,
                'name'         => "Cash on delivery",
                'slug'         => "cash-on-delivery",
                'status'       => $status,
                'phone_number' => "",
                'created_at'   => $now,
            ],
            [
                'id'           => 2,
                'name'         => "bKash",
                'slug'         => "bkash",
                'status'       => $status,
                'phone_number' => "01000000000",
                'created_at'   => $now,
            ],
            [
                'id'           => 3,
                'name'         => "Nagad",
                'slug'         => "nagad",
                'status'       => $status,
                'phone_number' => "01000000000",
                'created_at'   => $now,
            ],
            [
                'id'           => 4,
                'name'         => "Card Payment",
                'slug'         => "cart-payment",
                'status'       => $status,
                'phone_number' => "01000000000",
                'created_at'   => $now,
            ]
        ]);
    }
}
