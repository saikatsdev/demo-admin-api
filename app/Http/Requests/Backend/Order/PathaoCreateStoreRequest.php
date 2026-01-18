<?php

namespace App\Http\Requests\Backend\Order;

use Illuminate\Foundation\Http\FormRequest;

class PathaoCreateStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            "name"              => ["required", "string"],
            "contact_name"      => ["required", "string"],
            "contact_number"    => ["required", "string"],
            "secondary_contact" => ["nullable", "string"],
            "address"           => ["required", "min:10", "max:65"],
            "city_id"           => ["required", "integer"],
            "zone_id"           => ["required", "integer"],
            "area_id"           => ["required", "integer"]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
