<?php

namespace App\Http\Requests\Backend\CMS;

use App\Enums\BannerEnum;
use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'       => ['nullable', "string"],
            'device_type' => ['required', 'string', Rule::in(BannerEnum::getAll())],
            "img_path"    => ["nullable"],
            'status'      => ['required', Rule::in(StatusEnum::activeStatus())],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
