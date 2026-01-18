<?php

namespace App\Http\Requests\Backend\Order;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            "name"            => ["required", "unique:coupons,name,$id"],
            "code"            => ["required", "unique:coupons,code,$id"],
            "discount_amount" => ["required"],
            "started_at"      => ["required"],
            "ended_at"        => ["required"],
            'status'          => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
