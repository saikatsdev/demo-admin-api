<?php

namespace App\Http\Resources\Backend\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Models\Order\OrderGuard;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $orderGuard = OrderGuard::where("status", StatusEnum::ACTIVE)->first();
        if ($orderGuard && !$this->is_permanent_unblock) {
            $orderCount = Order::where("block_user_id", $this->id)
            ->whereBetween("created_at", Helper::getStartAndEndTime($orderGuard->duration, $orderGuard->duration_type))
            ->count();

            // Unblock if conditions are not satisfied
            if ($orderCount != $orderGuard->quantity) {
                $this->update(["is_block" => false]);
            }
        }

        return [
            "id"                   => $this->id,
            "is_block"             => $this->is_block,
            "user_token"           => $this->user_token,
            "is_permanent_block"   => $this->is_permanent_block,
            "is_permanent_unblock" => $this->is_permanent_unblock,
            "created_at"           => $this->created_at,
            "details"              => $this->whenLoaded("details"),
        ];
    }
}
