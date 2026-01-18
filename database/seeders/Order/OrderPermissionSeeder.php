<?php

namespace Database\Seeders\Order;

use App\Helpers\Helper;
use Illuminate\Database\Seeder;

class OrderPermissionSeeder extends Seeder
{
    public function run()
    {
        $rolesStructure = [
            'superadmin' => [
                'delivery-gateways'        => 'c,r,u,d',
                'payment-gateways'         => 'c,r,u,d',
                'districts'                => 'c,r,u,d',
                'statuses'                 => 'c,r,u,d',
                'customer-types'           => 'c,r,u,d',
                'order-froms'              => 'c,r,u,d',
                'cancel-reasons'           => 'c,r,u,d',
                'coupons'                  => 'c,r,u,d',
                'orders'                   => 'c,r,u,d',
                'return-and-damages'       => 'c,r,u,d',
                'couriers'                 => 'c,r,u,d',
                'free-delivery'            => 'c,r,u,d',
                'down-sells'               => 'c,r,u,d',
                'incomplete-orders'        => 'c,r,u,d',
                'online-payment-discounts' => 'c,r,u,d',
                'block-users'              => 'r,u',
                'order-guards'             => 'c,r,u,d',
                'pathao'                   => 'r,u',
                'stead-fast'               => 'r,u',
                'redx'                     => 'r,u',
            ],
            'staff' => [
                'delivery-gateways'        => 'c,r,u',
                'payment-gateways'         => 'c,r,u',
                'districts'                => 'c,r,u',
                'statuses'                 => 'c,r,u',
                'customer-types'           => 'c,r,u',
                'order-froms'              => 'c,r,u',
                'cancel-reasons'           => 'c,r,u',
                'coupons'                  => 'c,r,u',
                'orders'                   => 'c,r,u',
                'return-and-damages'       => 'c,r,u',
                'online-payment-discounts' => 'c,r,u',
            ],
        ];

        Helper::createRolePermission($rolesStructure, "Order", $this->command);
    }
}
