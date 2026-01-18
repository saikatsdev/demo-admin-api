<?php

namespace App\Http\Requests\Backend\Order;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class FreeDeliveryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "quantity" => ["nullable"],
            "price"    => ["nullable"],
            "status"   => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
