<?php

namespace App\Http\Requests\Backend\Product;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CampaignRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            "title"                 => ["required", "string", "unique:campaigns,title,$id"],
            "start_date"            => ["required"],
            "end_date"              => ["required"],
            "items"                 => ["required", "array"],
            "items.*.product_id"    => ["required"],
            "items.*.discount"      => ["required"],
            "items.*.discount_type" => ["required", "in:fixed,percentage"],
            'status'                => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
