<?php

namespace App\Http\Requests\Backend\Order;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CancelReasonRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            'name'   => ['required', "unique:cancel_reasons,name,$id"],
            'status' => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
