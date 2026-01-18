<?php

namespace App\Http\Requests\Backend\Order;

use App\Enums\StatusEnum;
use App\Enums\DiscountTypeEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OnlinePaymentDiscountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payment_gateway_id'      => ['required', 'exists:payment_gateways,id'],
            'discount_amount'         => ['nullable', 'numeric'],
            'minimum_cart_amount'     => ['nullable', 'numeric'],
            'maximum_discount_amount' => ['required', 'numeric'],
            'discount_type'           => ['required', Rule::in(DiscountTypeEnum::getAll())],
            'status'                  => ['required', Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
