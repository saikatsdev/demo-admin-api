<?php

namespace App\Http\Requests\Backend\Order;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DownSellRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "title"        => ["required"],
            "amount"       => ["required"],
            "duration"     => ["required"],
            "started_at"   => ["required"],
            "ended_at"     => ["required"],
            "status"       => ["required", Rule::in(StatusEnum::activeStatus())],
            "product_ids"  => ["nullable", "array"],
            "category_ids" => ["nullable", "array"],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
