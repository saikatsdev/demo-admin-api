<?php

namespace Database\Seeders\Order;

use App\Enums\StatusEnum;
use App\Enums\DiscountTypeEnum;
use Illuminate\Database\Seeder;
use App\Models\Order\OnlinePaymentDiscount;

class OnlinePaymentDiscountSeeder extends Seeder
{
    public function run()
    {
        OnlinePaymentDiscount::insert([
            [
                "payment_gateway_id"      => 2,
                "discount_type"           => DiscountTypeEnum::PERCENTAGE,
                "discount_amount"         => 5,
                "minimum_cart_amount"     => 200,
                "maximum_discount_amount" => 300,
                "status"                  => StatusEnum::ACTIVE
            ],
            [
                "payment_gateway_id"      => 3,
                "discount_type"           => DiscountTypeEnum::FIXED,
                "discount_amount"         => 50,
                "minimum_cart_amount"     => 200,
                "maximum_discount_amount" => 0,
                "status"                  => StatusEnum::ACTIVE
            ],
            [
                "payment_gateway_id"      => 4,
                "discount_type"           => DiscountTypeEnum::PERCENTAGE,
                "discount_amount"         => 5,
                "minimum_cart_amount"     => 200,
                "maximum_discount_amount" => 300,
                "status"                  => StatusEnum::ACTIVE
            ]
        ]);
    }
}
