<?php

namespace App\Http\Requests\Backend\Product;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        $rules = [
            "name"                              => ["required", "unique:products,name,{$id}"],
            "brand_id"                          => ["nullable", Rule::exists('brands', 'id')],
            "category_id"                       => ["required", "integer",  Rule::exists('categories', 'id')],
            "status"                            => ["required", Rule::in(StatusEnum::activeStatus())],
            "buy_price"                         => ["nullable", "numeric", "min:0"],
            "mrp"                               => ["nullable", "numeric", "min:0"],
            "current_stock"                     => ["nullable", "numeric"],
            "free_shipping"                     => ["boolean"],
            "up_sell_product_ids"               => ["nullable", "array"],
            "gallery_images"                    => ["nullable", "array"],
            "gallery_images.*"                  => ["image", "mimes:jpeg,jpg,png,gif,webp"],
            "variations"                        => ["required_if:buy_price,0", "required_if:mrp,0", "array"],
            "variations.*.attribute_value_id_1" => ["nullable", "integer"],
            "variations.*.attribute_value_id_2" => ["nullable", "integer"],
            "variations.*.attribute_value_id_3" => ["nullable", "integer"],
            "variations.*.buy_price"            => ["required_with:variations", "numeric", "min:0"],
            "variations.*.mrp"                  => ["required_with:variations", "numeric", "min:0"],
        ];

        if (!$id) {
            $rules["image"] = ["required", "image", "mimes:jpeg,jpg,png,gif,webp"];
        } else {
            $rules["image"] = ["nullable", "image", "mimes:jpeg,jpg,png,gif,webp"];
        }

        return $rules;
    }

    public function authorize()
    {
        return true;
    }
}
