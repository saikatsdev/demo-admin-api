<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class BlockUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "is_block"             => ["required", "boolean"],
            "is_permanent_block"   => ["required", "boolean"],
            "is_permanent_unblock" => ["required", "boolean"],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
