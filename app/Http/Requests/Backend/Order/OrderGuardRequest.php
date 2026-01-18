<?php

namespace App\Http\Requests\Backend\Order;

use App\Enums\StatusEnum;
use App\Enums\DurationTypeEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OrderGuardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quantity'         => ['required'],
            'duration'         => ['required'],
            'allow_percentage' => ['required'],
            'block_message'    => ['required'],
            'duration_type'    => ['required', Rule::in(DurationTypeEnum::getAll())],
            'status'           => ['required', Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
