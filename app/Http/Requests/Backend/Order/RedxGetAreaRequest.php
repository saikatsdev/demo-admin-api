<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class RedxGetAreaRequest extends FormRequest
{
    public function rules()
    {
        return [
            "zone_id"       => ["nullable", "integer"],
            "post_code"     => ["nullable"],
            "district_name" => ["nullable"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
