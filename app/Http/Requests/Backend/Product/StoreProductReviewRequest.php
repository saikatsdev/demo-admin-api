<?php

namespace App\Http\Requests\Backend\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'min:2', 'max:250'],
            'product_id' => ['required', 'exists:products,id'],
            'image'      => ['nullable', 'mimes:png,jpg,jpeg,webp', 'max:2048']
        ];
    }
}
