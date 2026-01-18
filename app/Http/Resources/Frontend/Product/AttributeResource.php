<?php

namespace App\Http\Resources\Frontend\Product;


use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'attribute_values' => $this->attributeValues->map(function ($value) {
                $productCount = $value->product_count_1 + $value->product_count_2 + $value->product_count_3;

                // Only include values where the total is greater than 0
                if ($productCount > 0) {
                    return [
                        'id'             => $value->id,
                        'value'          => $value->value,
                        'slug'           => $value->slug,
                        'products_count' => $productCount,
                    ];
                }

                return null;
            })->filter(), // Remove null values from the collection
        ];
    }
}
