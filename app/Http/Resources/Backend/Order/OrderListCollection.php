<?php

namespace App\Http\Resources\Backend\Order;

use App\Models\Order\Order;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderListCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $totalOrderCount  = Order::count();
        $totalOrderAmount = Order::sum("payable_price");

        $response = [
            "orders_count"           => $totalOrderCount,
            "duplicate_orders_count" => Order::where("is_duplicate", 1)->where("current_status_id", OrderStatusEnum::PENDING)->count(),
            "total_amount"           => $totalOrderAmount,
            'data' => $this->collection->transform(function ($order) {

                $order->products = $order->details
                    ->pluck('product')
                    ->filter()
                    ->map(fn($product) => new OrderProductResource($product))
                    ->values();

                unset($order->details);

                return $order;
            }),
        ];

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

        return $response;
    }
}
