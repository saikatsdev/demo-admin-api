<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class SteadFastBulkCreateOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            "order_ids"   => ["required", "array"],
            "order_ids.*" => ["exists:orders,id"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
