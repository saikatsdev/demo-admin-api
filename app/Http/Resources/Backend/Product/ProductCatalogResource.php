<?php

namespace App\Http\Resources\Backend\Product;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCatalogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                 => $this->id,
            "name"               => $this->name,
            "slug"               => $this->slug,
            "number_of_products" => $this->number_of_products,
            "status"             => $this->status,
            "url"                => Helper::getFilePath($this->url),
            "catalog_type"       => $this->whenLoaded("catalogType"),
            "categories"         => $this->whenLoaded("categories"),
            "created_by"         => $this->whenLoaded("createdBy"),
        ];
    }
}
