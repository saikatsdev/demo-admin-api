<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function rules()
    {
        return [
            "email" => ["required", "email"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
