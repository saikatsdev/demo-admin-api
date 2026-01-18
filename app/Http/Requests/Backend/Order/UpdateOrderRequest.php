<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'payment_gateway_id'              => ['nullable', Rule::exists('payment_gateways', 'id')],
            'delivery_gateway_id'             => ['nullable', Rule::exists('delivery_gateways', 'id')],
            'current_status_id'               => ['nullable', Rule::exists('statuses', 'id')],
            'coupon_id        '               => ['nullable', Rule::exists('coupons', 'id')],
            'customer_name'                   => ['required', 'string', 'max:100'],
            'phone_number'                    => ['required', 'string'],
            'address_details'                 => ['required', 'string', 'max:250'],
            'delivery_charge'                 => ['required', 'min:0'],
            'items'                           => ['required', 'array'],
            'items.*.product_id'              => ['required', 'integer'],
            'items.*.attribute_value_id_1'    => ['nullable'],
            'items.*.attribute_value_id_2'    => ['nullable'],
            'items.*.attribute_value_id_3'    => ['nullable'],
            'items.*.quantity'                => ['required', 'integer'],
            'items.*.buy_price'               => ['required', 'numeric'],
            'items.*.mrp'                     => ['required', 'numeric'],
            'items.*.sell_price'              => ['required', 'numeric'],
            'items.*.discount'                => ['required', 'numeric', 'min:0'],
            "raw_materials"                   => ["nullable", "array"],
            "raw_materials.*.raw_material_id" => ["required", "integer"],
            "raw_materials.*.quantity"        => ["nullable", "required_with:raw_materials.*.raw_material_id", "integer"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
