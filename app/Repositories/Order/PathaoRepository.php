<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\Order;
use App\Models\Order\Pathao;
use App\Enums\OrderStatusEnum;
use App\Models\Order\PathaoArea;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PathaoRepository
{
    private $endPoint;
    private $clientId;
    private $clientSecret;
    private $username;
    private $password;
    private $grantType;

    public function __construct()
    {
        $this->endPoint     = config("pathao.endpoint");
        $this->clientId     = config("pathao.client_id");
        $this->clientSecret = config("pathao.client_secret");
        $this->username     = config("pathao.username");
        $this->password     = config("pathao.password");
        $this->grantType    = config("pathao.grant_type");
    }

    // Get access token
    public function accessToken()
    {
        $headers = [
            "Accept"       => "application/json",
            "Content-Type" => "application/json"
        ];

        $body = [
            "client_id"     => $this->clientId,
            "client_secret" => $this->clientSecret,
            "username"      => $this->username,
            "password"      => $this->password,
            "grant_type"    => $this->grantType
        ];

        $url = "$this->endPoint/aladdin/api/v1/issue-token";

        $res = Http::withHeaders($headers)->post($url, $body);

        return json_decode($res, true);
    }

    public function headers()
    {
        $data        = $this->accessToken();

        $accessToken = @$data["access_token"];

        return [
            "Authorization" => "Bearer $accessToken",
            "Accept"        => "application/json",
            "Content-Type"  => "application/json"
        ];
    }

    // Get refresh token
    public function refreshToken()
    {
        $headers = [
            "Accept"       => "application/json",
            "Content-Type" => "application/json"
        ];

        $data         = $this->accessToken();
        $refreshToken = @$data["refresh_token"];

        $body = [
            "client_id"     => $this->clientId,
            "client_secret" => $this->clientSecret,
            "refresh_token" => $refreshToken,
            "grant_type"    => "refresh_token"
        ];

        $url = "$this->endPoint/aladdin/api/v1/issue-token";

        $res = Http::withHeaders($headers)->post($url, $body);

        return json_decode($res, true);
    }

    public function createOrder($request)
    {
        $orderId = $request->input("order_id", null);

        if (!env("PATHAO_CLIENT_ID") || !env("PATHAO_CLIENT_SECRET")) {
            throw new CustomException("Pathao credential not configured");
        }

        $order = Order::where("courier_id", 2)
        ->where("id", $orderId)
        ->whereNotNull("pickup_store_id")
        ->whereNotNull("courier_area_id")
        ->first();

        $pathaoArea = PathaoArea::find($request->courier_area_id);

        if (!$order || !$pathaoArea) {
            throw new CustomException("Invalid pathao courier information for order id $orderId");
        }

        $body = [
            "store_id"            => $order->pickup_store_id,
            "merchant_order_id"   => $order->id,
            "recipient_name"      => $order->customer_name,
            "recipient_phone"     => $order->phone_number,
            "recipient_address"   => $order->address_details,
            "recipient_city"      => $pathaoArea->area_value['city_id'],
            "recipient_zone"      => $pathaoArea->area_value['zone_id'],
            "recipient_area"      => $pathaoArea->area_value['zone_id'],
            "delivery_type"       => $order->delivery_type ?? 48, // Normal delivery
            "item_type"           => 2, // Parcel
            "special_instruction" => "",
            "item_quantity"       => $order->details()->sum("quantity"),
            "item_weight"         => $order->item_weight ?? 0.5,
            "amount_to_collect"   => $order->paid_status == StatusEnum::PAID ? 0 : round($order->payable_price),
            "item_description"    => $order->note,
        ];

        $url = "$this->endPoint/aladdin/api/v1/orders";

        $res = Http::withHeaders($this->headers())->post($url, $body);

        $res = json_decode($res, true);

        if ($res["type"] == "success") {
            // Update order information
            $order->consignment_id    = @$res["data"]["consignment_id"] ?? null;
            $order->courier_payable   = @$res["data"]["delivery_fee"] ?? 0;
            $order->courier_status_id = OrderStatusEnum::COURIER_PENDING;
            $order->save();

            return $res;
        } else {
            $errorMessage = null;
            if (@$res["errors"]["store_id"]) {
                $errorMessage = @$res["errors"]["store_id"][0];
            } elseif (@$res["errors"]["recipient_name"]) {
                $errorMessage = @$res["errors"]["recipient_name"][0];
            } elseif (@$res["errors"]["recipient_phone"]) {
                $errorMessage = @$res["errors"]["recipient_phone"][0];
            } elseif (@$res["errors"]["sender_name"]) {
                $errorMessage = @$res["errors"]["sender_name"][0];
            } elseif (@$res["errors"]["sender_phone"]) {
                $errorMessage = @$res["errors"]["sender_phone"][0];
            } elseif (@$res["errors"]["recipient_city"]) {
                $errorMessage = @$res["errors"]["recipient_city"][0];
            } elseif (@$res["errors"]["recipient_zone"]) {
                $errorMessage = @$res["errors"]["recipient_zone"][0];
            } elseif (@$res["errors"]["recipient_address"]) {
                $errorMessage = @$res["errors"]["recipient_address"][0];
            } elseif (@$res["errors"]["amount_to_collect"]) {
                $errorMessage = @$res["errors"]["amount_to_collect"][0];
            } elseif (@$res["errors"]["item_weight"]) {
                $errorMessage = @$res["errors"]["item_weight"][0];
            } elseif (@$res["errors"]["item_type"]) {
                $errorMessage = @$res["errors"]["item_type"][0];
            } elseif (@$res["errors"]["delivery_type"]) {
                $errorMessage = @$res["errors"]["delivery_type"][0];
            } elseif (@$res["errors"]["item_quantity"]) {
                $errorMessage = @$res["errors"]["item_quantity"][0];
            } else {
                $errorMessage = "Invalid information";
            }

            throw new CustomException($errorMessage);
        }
    }

    public function createBulkOrder($request)
    {
        if (!env("PATHAO_CLIENT_ID") || !env("PATHAO_CLIENT_SECRET")) {
            throw new CustomException("Pathao credential not configured");
        }

        $orders = Order::where("courier_id", 2)->whereIn("id", $request->order_ids)->get();

        $data = [];

        foreach ($orders as $order) {
            $pathaoArea = PathaoArea::find($order->courier_area_id);

            if (!$pathaoArea) {
                return ;
            }

            $data[] = [
                "item_type"           => 2,
                "store_id"            => $order->pickup_store_id,
                "merchant_order_id"   => $order->id,
                "recipient_name"      => $order->customer_name,
                "recipient_phone"     => $order->phone_number,
                "recipient_zone"      => $pathaoArea->area_value['zone_id'],
                "recipient_city"      => $pathaoArea->area_value['city_id'],
                "recipient_area"      => $pathaoArea->area_value['area_id'],
                "recipient_address"   => $order->address_details,
                "amount_to_collect"   => $order->paid_status == StatusEnum::PAID ? 0 : round($order->payable_price),
                "item_quantity"       => $order->details()->sum("quantity"),
                "item_weight"         => $order->item_weight,
                "item_description"    => $order->note,
                "delivery_type"       => $order->delivery_type ?? 48,
                "special_instruction" => "",
            ];
        }

        $url = "$this->endPoint/aladdin/api/v1/orders/bulk";

        $res = Http::withHeaders($this->headers())->post($url, ["orders" => $data]);

        // Update courier current status
        Order::whereIn('id', $request->order_ids)->update(['courier_status_id' => OrderStatusEnum::COURIER_PENDING]);

        return json_decode($res, true);
    }

    public function getStores()
    {
        // Define the cache key and expiration time
        $cacheKey      = "pathao_stores_data";
        $cacheDuration = now()->addMinutes(60);  // Cache for 60 minutes
        
        // Check if the data exists in cache; if not, fetch and cache it
        return Cache::remember($cacheKey, $cacheDuration, function () {
            $url = "$this->endPoint/aladdin/api/v1/stores";
            $res = Http::withHeaders($this->headers())->get($url);

            return json_decode($res, true);
        });
    }

    public function createNewStore($request)
    {
        $pathaoArea = PathaoArea::find($request->courier_area_id);

        if(!$pathaoArea){
            throw new CustomException("Pathao area not found");
        }

        $body = [
            "name"              => $request->name,
            "contact_name"      => $request->contact_name,
            "contact_number"    => $request->contact_number,
            "secondary_contact" => $request->secondary_contact,
            "address"           => $request->address,
            "city_id"           => $pathaoArea->area_value['city_id'],
            "zone_id"           => $pathaoArea->area_value['zone_id'],
            "area_id"           => $pathaoArea->area_value['area_id']
        ];

        $url = "$this->endPoint/aladdin/api/v1/stores";

        $res = Http::withHeaders($this->headers())->post($url, $body);

        return json_decode($res, true);
    }

    public function orderShortInfo($consignmentId)
    {
        $url = "$this->endPoint/aladdin/api/v1/orders/$consignmentId/info";

        $res = Http::withHeaders($this->headers())->get($url);

        return json_decode($res, true);
    }

    public function getCities()
    {
        $cacheKey = "city_list";

        $cacheDuration = now()->addDay(7);

        // Use Cache::remember to either fetch from the cache or make the request and store the result
        return Cache::remember($cacheKey, $cacheDuration, function () {
            $url = "$this->endPoint/aladdin/api/v1/countries/1/city-list";

            $res = Http::withHeaders($this->headers())->get($url);

            return json_decode($res, true);
        });
    }

    public function getZones($cityId)
    {
        $cacheKey = "zone_list_{$cityId}";

        $cacheDuration = now()->addDay(7);

        // Use Cache::remember to either fetch from the cache or make the request and store the result
        return Cache::remember($cacheKey, $cacheDuration, function () use ($cityId) {
            $url = "$this->endPoint/aladdin/api/v1/cities/{$cityId}/zone-list";

            $res = Http::withHeaders($this->headers())->get($url);

            return json_decode($res, true);
        });
    }

    public function getAreas($zoneId)
    {
        $cacheKey = "area_list_{$zoneId}";

        $cacheDuration = now()->addDay(7);

        // Use Cache::remember to either fetch from the cache or make the request and store the result
        return Cache::remember($cacheKey, $cacheDuration, function () use ($zoneId) {
            $url = "$this->endPoint/aladdin/api/v1/zones/{$zoneId}/area-list";

            $res = Http::withHeaders($this->headers())->get($url);

            return json_decode($res, true);
        });
    }

    public function priceCalculation($request)
    {
        $body = [
            "store_id"       => $request->store_id,
            "item_type"      => $request->item_type,
            "delivery_type"  => $request->delivery_type,
            "item_weight"    => $request->item_weight,
            "recipient_city" => $request->recipient_city_id,
            "recipient_zone" => $request->recipient_zone_id
        ];

        $url = "$this->endPoint/aladdin/api/v1/merchant/price-plan";

        $res = Http::withHeaders($this->headers())->post($url, $body);

        return json_decode($res, true);
    }

    public function updateEnvCredential($request)
    {
        $data = [
            "PATHAO_ENDPOINT"      => $request->pathao_endpoint,
            "PATHAO_CLIENT_ID"     => $request->pathao_client_id,
            "PATHAO_CLIENT_SECRET" => $request->pathao_client_secret,
            "PATHAO_USERNAME"      => $request->pathao_username,
            "PATHAO_PASSWORD"      => $request->pathao_password,
            "PATHAO_GRANT_TYPE"    => $request->pathao_grant_type,
        ];

        Helper::updateEnvVariable($data);

        $pathao = Pathao::firstOrNew();

        $pathao->endpoint      = $request->pathao_endpoint;
        $pathao->client_id     = $request->pathao_client_id;
        $pathao->client_secret = $request->pathao_client_secret;
        $pathao->username      = $request->pathao_username;
        $pathao->password      = $request->pathao_password;
        $pathao->grant_type    = $request->pathao_grant_type;
        $pathao->save();

        return $pathao;
    }

    public function show()
    {
        $pathao = Pathao::with(["createdBy:id,username", "updatedBy:id,username"])->first();

        if (!$pathao) {
            throw new CustomException("Pathao Credential not found");
        }

        return $pathao;
    }

    public function callback($request)
    {
        $requestEvent  = $request->event;

        $order = Order::where("consignment_id", $request->consignment_id)
        ->orWhere("id", $request->merchant_order_id)
        ->first();

        if (!$order) {
            return false;
        }

        if ($requestEvent == "order.in-transit") {
            $order->courier_status_id = OrderStatusEnum::COURIER_PENDING;
        } elseif ($requestEvent == "order.delivered") {
            $order->current_status_id = OrderStatusEnum::DELIVERED;
            $order->courier_status_id = OrderStatusEnum::DELIVERED;
        } elseif ($requestEvent == "order.returned") {
            $order->current_status_id = OrderStatusEnum::PENDING_RETURNED;
            $order->courier_status_id = OrderStatusEnum::PENDING_RETURNED;
        } elseif ($requestEvent == "order.partial-delivery") {
            $order->current_status_id = OrderStatusEnum::PARTIAL_RETURNED;
            $order->courier_status_id = OrderStatusEnum::PARTIAL_RETURNED;
        }

        $existingResponses = $order->callback_response ?? [];
        if (!is_array($existingResponses)) {
            $existingResponses = json_decode($existingResponses, true) ?? [];
        }

        $existingResponses[] = [
            "consignment_id"    => $request->consignment_id,
            "merchant_order_id" => $request->merchant_order_id,
            "updated_at"        => $request->updated_at,
            "timestamp"         => $request->timestamp,
            "store_id"          => $request->store_id,
            "event"             => $requestEvent,
            "collected_amount"  => $request->collected_amount,
            "reason"            => $request->reason
        ];

        $order->callback_response = $existingResponses;
        $order->save();

        return true;
    }
    
    public function searchArea($request)
    {
        $term = trim($request->area_name ?? '');

        $parts = collect(explode(',', $term))
            ->map(fn ($p) => strtolower(trim($p)))
            ->filter()
            ->values();

        return PathaoArea::query()
            ->when($parts->isNotEmpty(), function ($query) use ($parts) {
                $query->where(function ($q) use ($parts) {
                    foreach ($parts as $part) {
                        $safe = str_replace(['%', '_'], ['\%', '\_'], $part);

                        $q->orWhereRaw(
                            'LOWER(area_name) LIKE ?',
                            ["%{$safe}%"]
                        );
                    }
                });

                $query->orderByRaw("
                    CASE
                        WHEN LOWER(area_name) LIKE ? THEN 1
                        WHEN LOWER(area_name) LIKE ? THEN 2
                        ELSE 3
                    END
                ", [
                    '%' . ($parts[0] ?? '') . '%',
                    '%' . ($parts[1] ?? '') . '%',
                ]);
            })
            ->when($request->city_id, fn ($q) =>
                $q->where('area_value->city_id', $request->city_id)
            )
            ->when($request->zone_id, fn ($q) =>
                $q->where('area_value->zone_id', $request->zone_id)
            )
            ->when($request->area_id, fn ($q) =>
                $q->where('area_value->area_id', $request->area_id)
            )
            ->limit(20)
            ->get();
    }
}
