<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class RedxParcelCreateRequest extends FormRequest
{
    public function rules()
    {
        return [
            "order_id" => ["required", "exists:orders,id"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
