<?php

namespace App\Http\Requests\Backend\CMS;

use App\Enums\SliderEnum;
use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "title"    => ["nullable", "string"],
            "img_path" => ["nullable", Rule::in(SliderEnum::getAll())],
            'status'   => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
