<?php

namespace App\Repositories\Product;

use App\Models\Product\UpSellSetting;

class UpSellSettingRepository
{
    public function __construct(protected UpSellSetting $model){}

    public function index($request)
    {
        $setting = $this->model::first();

        return $setting;
    }

    public function update($request)
    {
        $setting = $this->model::firstOrNew();

        $setting->greetings         = $request->greetings;
        $setting->title             = $request->title;
        $setting->sub_title         = $request->sub_title;
        $setting->button_text       = $request->button_text;
        $setting->button_text_color = $request->button_text_color;
        $setting->button_bg_color   = $request->button_bg_color;

        $setting->save();

        return $setting;
    }
}
