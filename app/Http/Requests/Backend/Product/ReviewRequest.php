<?php

namespace App\Http\Requests\Backend\Product;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required'],
            'rate'       => ['required', 'numeric', "min:1", "max:5"],
            'comment'    => ['nullable', "string"],
            'status'     => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
