<?php

namespace App\Http\Requests\Backend\Product;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            'name'   => ['required', "unique:brands,name,$id"],
            'image'  => ['nullable'],
            'status' => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
