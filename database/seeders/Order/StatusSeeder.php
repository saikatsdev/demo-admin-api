<?php

namespace Database\Seeders\Order;

use App\Models\Order\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        Status::insert([
            [
                "name"       => "Pending",
                "slug"       => "pending",
                "bg_color"   => "#ddb063",
                "text_color" => "#ffffff",
                "position"   => 1,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "name"       => "On Hold",
                "slug"       => "on-hold",
                "bg_color"   => "#C98209",
                "text_color" => "#ffffff",
                "position"   => 2,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "name"       => "Approved",
                "slug"       => "approved",
                "bg_color"   => "#06d14a",
                "text_color" => "#ffffff",
                "position"   => 3,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "name"       => "Ready To Picked",
                "slug"       => "picked",
                "bg_color"   => "#CDDC39",
                "text_color" => "#ffffff",
                "position"   => 4,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "name"       => "On Way",
                "slug"       => "on-way",
                "bg_color"   => "#673AB7",
                "text_color" => "#ffffff",
                "position"   => 5,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "name"       => "Stock Pending",
                "slug"       => "stock-pending",
                "bg_color"   => "#673AB7",
                "text_color" => "#ffffff",
                "position"   => 6,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "name"       => "Delivered",
                "slug"       => "delivered",
                "bg_color"   => "#4CAF50",
                "text_color" => "#ffffff",
                "position"   => 7,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "name"       => "Canceled",
                "slug"       => "canceled",
                "bg_color"   => "#F44336",
                "text_color" => "#ffffff",
                "position"   => 8,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "name"       => "Pending Returned",
                "slug"       => "pending-returned",
                "bg_color"   => "#9C27B0",
                "text_color" => "#ffffff",
                "position"   => 9,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "name"       => "Returned",
                "slug"       => "returned",
                "bg_color"   => "#9C27B0",
                "text_color" => "#ffffff",
                "position"   => 10,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "name"       => "Damaged",
                "slug"       => "damaged",
                "bg_color"   => "#9C27B0",
                "text_color" => "#ffffff",
                "position"   => 11,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                'name'       => 'Partial Returned',
                'slug'       => 'partial-returned',
                'bg_color'   => '#9C27B0',
                'text_color' => '#ffffff',
                'position'   => 12,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                'slug'       => 'courier-pending',
                'name'       => 'Courier Pending',
                'bg_color'   => '#b07027ff',
                'text_color' => '#ffffff',
                'position'   => 13,
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                'slug'       => 'courier-received',
                'name'       => 'Courier Received',
                'bg_color'   => '#b07027ff',
                'text_color' => '#ffffff',
                'position'   => 14,
                "created_at" => $now,
                "updated_at" => $now
            ],
        ]);
    }
}
