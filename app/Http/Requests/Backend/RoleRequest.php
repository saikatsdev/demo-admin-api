<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            'display_name'   => ['required', "unique:roles,display_name,$id"],
            'permission_ids' => ['nullable', 'array']
        ];
    }
}
