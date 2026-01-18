<?php

namespace App\Http\Requests\Backend;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            'username'     => ['required'],
            'phone_number' => ['required', "unique:users,phone_number,$id"],
            'email'        => ['nullable', "unique:users,email,$id"],
            'status'       => ['required', Rule::in(StatusEnum::activeStatus())],
            'role_ids'     => ['required', 'array']
        ];
    }
}
