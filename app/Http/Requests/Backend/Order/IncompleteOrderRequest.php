<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class IncompleteOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name"         => ["nullable", "string"],
            "phone_number" => ["required", "string"],
            "address"      => ["nullable", "string"],
            "status_id"    => ["required", "exists:statuses,id"],
            "items"        => ["nullable", "array"]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
