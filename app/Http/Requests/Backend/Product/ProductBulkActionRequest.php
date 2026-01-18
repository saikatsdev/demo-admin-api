<?php

namespace App\Http\Requests\Backend\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductBulkActionRequest extends FormRequest
{
    public function rules()
    {
        return [
            "product_ids"   => ["required", "array"],
            "product_ids.*" => ["exists:products,id"],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
