<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Models\Order\Order;
use App\Enums\OrderStatusEnum;
use App\Models\Order\SteadFast;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Http;

class SteadFastRepository
{
    private $endPoint;
    private $apiKey;
    private $secretKey;
    private $headers;

    public function __construct()
    {
        $this->endPoint  = config("stead_fast.endpoint");
        $this->apiKey    = config("stead_fast.api_key");
        $this->secretKey = config("stead_fast.secret_key");
        $this->headers   = $this->getHeaders();
    }

    public function createOrder($request)
    {
        if (!env("STEAD_FAST_API_KEY") || !env("STEAD_FAST_SECRET_KEY")) {
            throw new CustomException("Stead fast credential not configured");
        }

        $orderId = $request->input('order_id', null);

        $order = Order::where("courier_id", 1)
        ->where("id", $orderId)
        ->first();

        if (!$order) {
            throw new CustomException("Invalid steadfast courier information for order id $orderId");
        }

        $body = [
            'invoice'           => $orderId,
            'recipient_name'    => $order->customer_name,
            'recipient_phone'   => $order->phone_number,
            'recipient_address' => $order->address_details,
            'cod_amount'        => round($order->payable_price),
            'note'              => $order->note
        ];

        $url = "$this->endPoint/create_order";

        $res = Http::withHeaders($this->headers)->post($url, $body);

        $res = json_decode($res, true);

        // Check error
        if ($res["status"] === 400) {
            $error = "Error from stead fast";
            $error = @$res["errors"]["invoice"];

            throw new CustomException($error);
        }

        // Update order information
        if ($res["status"] === 200) {
            $order->consignment_id    = @$res["consignment"]["consignment_id"];
            $order->tracking_code     = @$res["consignment"]["tracking_code"];
            $order->courier_status_id = OrderStatusEnum::COURIER_PENDING;
            $order->save();
        }

        return $res;
    }

    public function bulkCreate($request)
    {
        if (!env("STEAD_FAST_API_KEY") || !env("STEAD_FAST_SECRET_KEY")) {
            throw new CustomException("Stead fast credential not configured");
        }

        $orders = Order::where("courier_id", 1)
            ->whereIn("id", $request->order_ids)
            ->get();

        if ($orders->isEmpty()) {
            throw new CustomException("No valid orders found for SteadFast");
        }

        $payload = [];

        foreach ($orders as $order) {
            $payload[] = [
                'invoice'           => (string) $order->id,
                'recipient_name'    => $order->customer_name,
                'recipient_phone'   => $order->phone_number,
                'recipient_address' => $order->address_details,
                'cod_amount'        => (int) round($order->payable_price),
                'note'              => $order->note,
            ];
        }

        $url = "{$this->endPoint}/create_order/bulk-order";

        $res = Http::withHeaders($this->headers)->post($url, $payload);

        $responseData = $res->json();

        if (empty($responseData['data'])) {
            throw new CustomException(
                $responseData['message'] ?? 'SteadFast bulk order failed'
            );
        }

        foreach ($responseData['data'] as $item) {
            $order = Order::find($item['invoice'] ?? null);

            if ($order) {
                $order->consignment_id    = $item['consignment_id'] ?? null;
                $order->tracking_code     = $item['tracking_code'] ?? null;
                $order->courier_status_id = OrderStatusEnum::COURIER_PENDING;
                $order->save();
            }
        }

        return true;
    }

    public function getDeliveryStatus($invoiceId)
    {
        $url = "{$this->endPoint}/status_by_invoice/{$invoiceId}";

        $res = Http::withHeaders($this->headers)->get($url);

        $jsonRes = json_decode($res, true);

        return $jsonRes;
    }

    public function getCurrentBalance()
    {
        $url = "$this->endPoint/get_balance";

        $res = Http::withHeaders($this->headers)->get($url);

        $jsonRes = json_decode($res, true);

        return $jsonRes;
    }

    private function getHeaders()
    {
        return [
            "Api-Key"      => $this->apiKey,
            "Secret-Key"   => $this->secretKey,
            "Accept"       => "application/json",
            "Content-Type" => "application/json"
        ];
    }

    public function updateEnvCredential($request)
    {
        $data = [
            'STEAD_FAST_ENDPOINT'   => $request->stead_fast_endpoint,
            'STEAD_FAST_API_KEY'    => $request->stead_fast_api_key,
            'STEAD_FAST_SECRET_KEY' => $request->stead_fast_secret_key
        ];

        Helper::updateEnvVariable($data);

        $steadFast = SteadFast::firstOrNew();

        $steadFast->endpoint   = $request->stead_fast_endpoint;
        $steadFast->api_key    = $request->stead_fast_api_key;
        $steadFast->secret_key = $request->stead_fast_secret_key;
        $steadFast->save();

        return $steadFast;
    }

    public function show()
    {
        $steadFast =  SteadFast::with([
            "createdBy:id,username",
            "updatedBy:id,username"
        ])->first();

        if (!$steadFast) {
            throw new CustomException("Credential not found");
        }

        return $steadFast;
    }

    public function callback($request)
    {
        if ($request->notification_type == "delivery_status") {
            $steadFastStatus = $request->status;

            $order = Order::where("consignment_id", $request->consignment_id)->first();

            if (!$order) {
                throw new CustomException("Callback order not found");
            }

            if ($steadFastStatus == "Delivered" || $steadFastStatus == "delivered") {
                $order->current_status_id = OrderStatusEnum::DELIVERED;
                $order->courier_status_id = OrderStatusEnum::DELIVERED;
            } else if ($steadFastStatus == "partial_delivered") {
                $order->current_status_id = OrderStatusEnum::PARTIAL_RETURNED;
                $order->courier_status_id = OrderStatusEnum::PARTIAL_RETURNED;
            } else if ($steadFastStatus == "cancelled") {
                $order->current_status_id = OrderStatusEnum::PENDING_RETURNED;
                $order->courier_status_id = OrderStatusEnum::RETURNED;
            } else if ($steadFastStatus == "pending") {
                $order->courier_status_id = OrderStatusEnum::COURIER_RECEIVED;
            }

            $existingResponses = $order->callback_response ?? [];
            if (!is_array($existingResponses)) {
                $existingResponses = json_decode($existingResponses, true) ?? [];
            }

            $existingResponses[] = [
                "notification_type" => $request->notification_type,
                "consignment_id"    => $request->consignment_id,
                "invoice"           => $request->invoice,
                "status"            => $request->status,
                "cod_amount"        => $request->cod_amount,
                "delivery_charge"   => $request->delivery_charge,
                "updated_at"        => $request->updated_at,
                "note"              => $request->tracking_message
            ];

            $order->callback_response = $existingResponses;
            $order->courier_payable   = $request->delivery_charge;
            $order->save();

            return true;
        }

        return false;
    }
}
