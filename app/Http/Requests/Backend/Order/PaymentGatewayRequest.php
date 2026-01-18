<?php

namespace App\Http\Requests\Backend\Order;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PaymentGatewayRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            'name'   => ['required', "unique:payment_gateways,name,{$id}"],
            'image'  => ['nullable'],
            'status' => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
