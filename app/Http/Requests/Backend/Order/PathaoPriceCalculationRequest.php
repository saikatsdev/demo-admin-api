<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class PathaoPriceCalculationRequest extends FormRequest
{
    public function rules()
    {
        return [
            "store_id"       => ["required"],
            "item_type"      => ["required"],
            "delivery_type"  => ["required"],
            "item_weight"    => ["required"],
            "recipient_city" => ["required"],
            "recipient_zone" => ["required"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
