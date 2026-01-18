<?php

namespace App\Http\Resources\Backend\Order;

use App\Helpers\Helper;
use App\Models\Order\Order;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $totalOrderCount  = Order::count();
        $totalOrderAmount = Order::sum("payable_price");
        if (Helper::isModuleActive("TeamManagement") && !Helper::isAdminOrTeamLead()) {
            $totalOrderCount  = Order::where("assign_user_id", Auth::id())->count();
            $totalOrderAmount = Order::where("assign_user_id", Auth::id())->sum("payable_price");
        }

        $response = [
            "orders_count"           => $totalOrderCount,
            "duplicate_orders_count" => Order::where("is_duplicate", 1)->where("current_status_id", OrderStatusEnum::PENDING)->count(),
            "total_amount"           => $totalOrderAmount,
            'data'                   => $this->collection,
        ];

        if ($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $response['links'] = [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ];

            $response['meta'] = [
                'current_page' => $this->currentPage(),
                'from'         => $this->firstItem(),
                'last_page'    => $this->lastPage(),
                'path'         => $request->url(),
                'per_page'     => $this->perPage(),
                'to'           => $this->lastItem(),
                'total'        => $this->total(),
            ];
        }

        return $response;
    }
}
