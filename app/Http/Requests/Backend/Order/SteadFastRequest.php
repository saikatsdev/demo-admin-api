<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class SteadFastRequest extends FormRequest
{
    public function rules()
    {
        return [
            "stead_fast_endpoint"   => ["required"],
            "stead_fast_api_key"    => ["required"],
            "stead_fast_secret_key" => ["required"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
