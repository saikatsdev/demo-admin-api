<?php

namespace App\Http\Requests\Backend\Order;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OrderFromRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            "name"   => ["required", "unique:order_froms,name,$id"],
            "status" => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
