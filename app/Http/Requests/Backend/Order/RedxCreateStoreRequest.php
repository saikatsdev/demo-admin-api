<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class RedxCreateStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            "name"    => ["required", "string"],
            "phone"   => ["required", "string"],
            "address" => ["required", "string"],
            'area_id' => ["required"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
