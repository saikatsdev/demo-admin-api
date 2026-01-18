<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class CourierDeliveryReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "phone_number" => ["required"]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
