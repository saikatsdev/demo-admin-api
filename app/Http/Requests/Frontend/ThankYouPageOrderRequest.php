<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ThankYouPageOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            "thank_you_page_offer_id"      => ["required", "exists:thank_you_page_offers,id"],
            "order_id"                     => ["required", "exists:orders,id"],
            "items"                        => ["required", "array"],
            "items.*.product_id"           => ["required", "integer"],
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
