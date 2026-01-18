<?php

namespace App\Http\Requests\Backend\CMS;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class FAQRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            'question' => ['required', "unique:faqs,question,$id"],
            'status'   => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
