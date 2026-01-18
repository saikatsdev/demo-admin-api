<?php

namespace App\Repositories\Order;

use App\Models\Order\Order;
use App\Exceptions\CustomException;
use App\Http\Resources\Frontend\Order\OrderTrackResource;
use App\Http\Resources\Frontend\Order\OrderTrackCollection;

class OrderTrackRepository
{
    public function __construct(protected Order $model){}

    public function index($request)
    {
        $query = $this->model
            ->with([
                'details.product',
                'statuses',
                'currentStatus',
                'paymentGateway',
                'deliveryGateway',
                'transaction',
                'courier',
                'district',
            ])
            ->when($request->order_id, fn($q) =>
                $q->where('invoice_number', $request->order_id)
            )
            ->when($request->phone_number, fn($q) =>
                $q->where('phone_number', $request->phone_number)
            )
            ->latest();

        if ($request->order_id) {
            $order = $query->first();

            if (!$order) {
                throw new CustomException('Order not found');
            }

            return new OrderTrackResource($order);
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            throw new CustomException('No orders found');
        }

        return new OrderTrackCollection($orders);
    }

}
