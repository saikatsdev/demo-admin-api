<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Validation\Rule;
use App\Enums\ReturnOrDamageTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class ReturnOrDamageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "order_id"                     => ["required", "exists:orders,id"],
            "status_id"                    => ["required", "exists:statuses,id", "in:9,10"],
            "reason"                       => ["required"],
            "type"                         => ["required", Rule::in(ReturnOrDamageTypeEnum::getAll())],
            "items"                        => ["required"],
            "items.*.product_id"           => ["required", "integer"],
            "items.*.attribute_value_id_1" => ["nullable"],
            "items.*.attribute_value_id_2" => ["nullable"],
            "items.*.attribute_value_id_3" => ["nullable"],
            "items.*.quantity"             => ["required", "integer"],
            "items.*.buy_price"            => ["required", "numeric"],
            "items.*.mrp"                  => ["required", "numeric"],
            "items.*.sell_price"           => ["required", "numeric"],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
