<?php

namespace Database\Seeders\Order;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Order\CustomerType;

class CustomerTypeSeeder extends Seeder
{
    public function run(): void
    {
        $status = StatusEnum::ACTIVE;

        CustomerType::upsert([
            [
                'id'          => 1,
                'name'        => 'New',
                'slug'        => 'new',
                'order_range' => 0,
                'status'      => $status,
            ],
            [
                'id'          => 2,
                'name'        => 'Regular',
                'slug'        => 'regular',
                'order_range' => 2,
                'status'      => $status,
            ],
            [
                'id'          => 3,
                'name'        => 'VIP',
                'slug'        => 'vip',
                'order_range' => 4,
                'status'      => $status,
            ],
            [
                'id'          => 4,
                'name'        => 'Elite',
                'slug'        => 'elite',
                'order_range' => 8,
                'status'      => $status,
            ],
        ], ['id'], ['name', 'slug', 'order_range', 'status']);
    }
}
