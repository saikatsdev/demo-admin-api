<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class UpSellOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            "order_id"                     => ["required", "exists:orders,id"],
            "items"                        => ["required", "array"],
            "items.*.up_sell_detail_id"    => ["required", "exists:up_sell_details,id"],
            "items.*.product_id"           => ["required", "exists:products,id"],
            "items.*.quantity"             => ["required", "integer"],
            "items.*.attribute_value_id_1" => ["nullable", "integer"],
            "items.*.attribute_value_id_2" => ["nullable", "integer"],
            "items.*.attribute_value_id_3" => ["nullable", "integer"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
