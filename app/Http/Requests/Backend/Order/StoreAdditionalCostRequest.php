<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdditionalCostRequest extends FormRequest
{
    public function rules()
    {
        return [
            "start_date" => ["required", "date"],
            "end_date"   => ["nullable", "date"],
            "cost"       => ["required", "numeric"],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
