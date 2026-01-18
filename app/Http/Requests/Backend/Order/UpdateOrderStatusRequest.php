<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function rules()
    {
        return [
            "order_ids"         => ["required", "array"],
            "order_ids.*"       => ["required", "integer", Rule::exists("orders", "id")],
            "current_status_id" => ["required", "integer", Rule::exists("statuses", "id")]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
