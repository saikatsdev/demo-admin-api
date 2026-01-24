<?php

namespace App\Repositories\Order;

use App\Models\Order\Order;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CourierDeliveryReportRepository
{
    public function courierDeliveryReport($request)
    {
        $phoneNumber = $request->input('phone_number', null);

        $cacheKey = "courier_delivery_report_$phoneNumber";
        $duration = config('app.cache_duration', 10);

        $orderSummary = Order::where('phone_number', $phoneNumber)
            ->selectRaw('
            COUNT(*) as total_order,
            SUM(payable_price) as total_amount,
            SUM(CASE WHEN current_status_id = ? THEN 1 ELSE 0 END) as delivered_orders,
            SUM(CASE WHEN current_status_id = ? THEN 1 ELSE 0 END) as canceled_orders,
            SUM(CASE WHEN current_status_id = ? THEN 1 ELSE 0 END) as returned_orders,
            SUM(CASE WHEN current_status_id = ? THEN payable_price ELSE 0 END) as delivered_amount,
            SUM(CASE WHEN current_status_id = ? THEN payable_price ELSE 0 END) as canceled_amount,
            SUM(CASE WHEN current_status_id = ? THEN payable_price ELSE 0 END) as returned_amount
        ',
        [
            // Counts
            OrderStatusEnum::DELIVERED,
            OrderStatusEnum::CANCELED,
            OrderStatusEnum::RETURNED,
            // Amount sums
            OrderStatusEnum::DELIVERED,
            OrderStatusEnum::CANCELED,
            OrderStatusEnum::RETURNED,
        ])
        ->first();

        return Cache::remember($cacheKey, now()->addMinutes($duration), function () use ($phoneNumber, $orderSummary) {
            $response = Http::withToken('test-code')
            ->post('https://api.bdcourier.com/test/courier-check', [
                'phone' => $phoneNumber,
            ]);

            return [
                'courier_delivery_report' => $response["data"] ?? [],
                'report'                  => $response["reports"] ?? [],
                'order_summary' => [
                    'phone_number'     => $phoneNumber,
                    'total_order'      => $orderSummary->total_order ?? 0,
                    'delivered_orders' => $orderSummary->delivered_orders ?? 0,
                    'canceled_orders'  => $orderSummary->canceled_orders ?? 0,
                    'returned_orders'  => $orderSummary->returned_orders ?? 0,
                    'total_amount'     => $orderSummary->total_amount ?? 0,
                    'delivered_amount' => $orderSummary->delivered_amount ?? 0,
                    'canceled_amount'  => $orderSummary->canceled_amount ?? 0,
                    'returned_amount'  => $orderSummary->returned_amount ?? 0,
                ],
            ];
        });
    }
}
