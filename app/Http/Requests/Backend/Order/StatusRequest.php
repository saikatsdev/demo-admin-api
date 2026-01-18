<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class StatusRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            'name' => ['required', "unique:statuses,name,$id"],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
