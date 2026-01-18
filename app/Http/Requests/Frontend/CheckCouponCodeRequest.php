<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class CheckCouponCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "coupon_code"       => ["required", "string", "max:20"],
            "cart_total_amount" => ["required", "numeric"]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
