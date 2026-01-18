<?php

namespace App\Http\Requests\Backend\Order;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CustomerTypeRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            'name'        => ['required', "unique:customer_types,name,$id"],
            'order_range' => ['required', 'integer', 'min:0'],
            'status'      => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
