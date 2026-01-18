<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderNoteRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            "order_id" => $id ? ["nullable"] : ["required", "exists:orders,id"],
            "note"     => ["required"]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
