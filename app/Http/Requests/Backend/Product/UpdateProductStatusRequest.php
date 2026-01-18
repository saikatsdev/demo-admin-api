<?php

namespace App\Http\Requests\Backend\Product;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductStatusRequest extends FormRequest
{
    public function rules()
    {
        return [
            "product_ids" => ["required", "array"],
            "status"      => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
