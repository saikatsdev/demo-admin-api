<?php

namespace App\Http\Requests\Backend\Product;

use Illuminate\Foundation\Http\FormRequest;

class WarrantyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'unique:warranties,name'],
            'days' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
