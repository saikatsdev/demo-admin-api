<?php

namespace App\Http\Requests\Backend\CMS;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PrivacyPolicyRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            'title'  => ['required', "unique:privacy_policies,title,$id"],
            'status' => ['required', Rule::in(StatusEnum::activeStatus())],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
