<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class SteadFastCreateOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            "order_id" => ["required", "integer"],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
