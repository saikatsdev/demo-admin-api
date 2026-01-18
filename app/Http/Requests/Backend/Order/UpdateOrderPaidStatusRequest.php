<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderPaidStatusRequest extends FormRequest
{
    public function rules()
    {
        return [
            "paid_status" => ["required", "in:paid,unpaid"],
            "order_ids"   => ["required", "array"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
