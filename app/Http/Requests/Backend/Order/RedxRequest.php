<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class RedxRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "redx_endpoint" => ["required"],
            "redx_token"    => ["required"],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
