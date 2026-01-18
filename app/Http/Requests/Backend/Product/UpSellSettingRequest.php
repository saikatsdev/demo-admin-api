<?php

namespace App\Http\Requests\Backend\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpSellSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'greetings'         => ['required'],
            'title'             => ['required'],
            'sub_title'         => ['required'],
            'button_text'       => ['required'],
            'button_text_color' => ['required'],
            'button_bg_color'   => ['required'],
        ];
    }
}
