<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\Redx;
use App\Models\Order\Order;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Http;

class RedxRepository
{
    protected $endpoint;
    protected $headers;

    public function __construct()
    {
        $this->endpoint = config("redx.endpoint");
        $this->headers  = $this->getHeaders();
    }

    public function getArea($request)
    {
        $postCode     = $request->input('post_code', null);
        $districtName = $request->input('district_name', null);
        $zoneId       = $request->input('zone_id', null);

        $endpoint = "$this->endpoint/v1.0.0-beta/areas?post_code=$postCode&district_name=$districtName&zone_id=$zoneId";

        $result  = Http::withHeaders($this->headers)->get($endpoint);

        return json_decode($result, true);
    }

    public function createPickupStore($request)
    {
        $body = [
            "name"    => $request->name,
            "phone"   => $request->phone,
            "address" => $request->address,
            'area_id' => $request->areaId
        ];

        $endpoint = "$this->endpoint/v1.0.0-beta/pickup/store";

        $result = Http::withHeaders($this->headers)->post($endpoint, $body);

        return json_decode($result, true);
    }

    public function getPickupStore()
    {
        $endpoint = "$this->endpoint/v1.0.0-beta/pickup/stores";

        $result = Http::withHeaders($this->headers)->get($endpoint);

        return json_decode($result, true);
    }

    public function getPickupStoreDetail($id)
    {
        $endpoint = "$this->endpoint/v1.0.0-beta/pickup/store/info/$id";

        $result = Http::withHeaders($this->headers)->get($endpoint);

        return json_decode($result, true);
    }

    public function parcelTrack($id)
    {
        $endpoint = "$this->endpoint/v1.0.0-beta/parcel/track/$id";

        $result = Http::withHeaders($this->headers)->get($endpoint);

        return json_decode($result, true);
    }

    public function parcelCreate($request)
    {
        $orderId = $request->input("order_id", null);

        $order = Order::where("courier_id", 3)
        ->where("id", $orderId)
        ->whereNotNull("pickup_store_id")
        ->whereNotNull("area_id")
        ->whereNotNull("delivery_area")
        ->first();

        if (!$order) {
            throw new CustomException("Invalid redx courier information for order id $orderId");
        }

        $parcelDetails = [];

        foreach ($order->details as $item) {
            $parcelDetails[] = [
                "name"     => @$item->product->name,
                "category" => @$item->product->category->name,
                "value"    => $item->sell_price
            ];
        }

        $body = [
            "customer_name"          => $order->customer_name,
            "customer_phone"         => $order->phone_number,
            "delivery_area"          => $order->delivery_area,
            "delivery_area_id"       => $order->area_id,
            "customer_address"       => $order->address_details,
            "merchant_invoice_id"    => $order->id,
            "cash_collection_amount" => $order->paid_status == StatusEnum::PAID ? 0 : round($order->payable_price),
            "parcel_weight"          => $order->parcel_weight . " kg",
            "instruction"            => $order->note,
            "value"                  => $order->delivery_charge,
            "is_closed_box"          => false,
            "pickup_store_id"        => $order->pickup_store_id,
            "parcel_details_json"    => $parcelDetails
        ];

        $endpoint = "$this->endpoint/v1.0.0-beta/parcel";

        $result = Http::withHeaders($this->headers)->post($endpoint, $body);

        return json_decode($result, true);
    }

    public function parcelDetail($id)
    {
        $endpoint = "$this->endpoint/v1.0.0-beta/parcel/info/$id";

        $result = Http::withHeaders($this->headers)->get($endpoint);

        return json_decode($result, true);
    }

    public function updateEnvCredential($request)
    {
        $data = [
            'REDX_ENDPOINT' => $request->redx_endpoint,
            'REDX_TOKEN'    => $request->redx_token
        ];

        Helper::updateEnvVariable($data);

        $redx = Redx::firstOrNew();

        $redx->endpoint = $request->redx_endpoint;
        $redx->token    = $request->redx_token;
        $redx->save();

        return $redx;
    }

    public function show()
    {
        $redx = Redx::with(["createdBy:id,username", "updatedBy:id,username"])->first();

        if (!$redx) {
            throw new CustomException("Redx credential not found");
        }

        return $redx;
    }

    private function getHeaders()
    {
        $token = config("redx.token");

        return [
            "API-ACCESS-TOKEN" => "Bearer {$token}",
            "Accept"           => "application/json",
            "Content-Type"     => "application/json"
        ];
    }
}
