<?php

namespace App\Http\Requests\Backend\CMS;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            "name"    => ["required", "unique:products,name,{$id}"],
           "status"  => ["required", Rule::in(StatusEnum::activeStatus())],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
