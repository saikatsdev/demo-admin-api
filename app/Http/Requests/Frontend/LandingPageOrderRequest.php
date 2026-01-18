<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class LandingPageOrderRequest extends FormRequest
{
    
    public function rules()
    {
        return [
            "customer_name"                => ["required", "string", "max:100"],
            "phone_number"                 => ["required", "digits:11"],
            "delivery_gateway_id"          => ["nullable", "integer"],
            "address_details"              => ["required", "string", "max:250"],
            "payment_gateway_id"           => ["required", "integer"],
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
