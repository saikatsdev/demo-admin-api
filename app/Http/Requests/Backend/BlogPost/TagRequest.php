<?php

namespace App\Http\Requests\Backend\BlogPost;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            "name"   => ["required", "string", "unique:tags,name,$id"],
            'status' => ['required', Rule::in(StatusEnum::activeStatus())],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
