<?php

namespace App\Http\Requests\Backend\Product;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductCatalogRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            "name"                    => ["required", "unique:product_catalogs,name,$id"],
            // "product_catalog_type_id" => ["required", Rule::exists("product_catalog_types", "id")],
            "category_ids"            => ["array", "nullable"],
            "status"                  => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
