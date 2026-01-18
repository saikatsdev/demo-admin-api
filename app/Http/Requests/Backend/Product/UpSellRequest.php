<?php

namespace App\Http\Requests\Backend\Product;

use App\Enums\StatusEnum;
use App\Enums\DiscountTypeEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpSellRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "title"                            => ["required"],
            "start_date"                       => ["nullable"],
            "end_date"                         => ["nullable", "after_or_equal:start_date"],
            "status"                           => ["required", Rule::in(StatusEnum::activeStatus())],
            "trigger_product_ids"              => ["nullable", "array", "exists:products,id"],
            "trigger_category_ids"             => ["nullable", "array", "exists:categories,id"],
            "up_sell_offers"                   => ["required", "array"],
            "up_sell_offers.*.product_id"      => ["required", "exists:products,id"],
            "up_sell_offers.*.custom_name"     => ["nullable"],
            "up_sell_offers.*.discount_type"   => ["required", Rule::in(DiscountTypeEnum::getAll())],
            "up_sell_offers.*.discount_amount" => ["required"],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
