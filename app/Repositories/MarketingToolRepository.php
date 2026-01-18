<?php

namespace App\Repositories;

use App\Models\MarketingTool;

class MarketingToolRepository
{
    public function __construct(protected MarketingTool $model){}

    public function index($request)
    {
        $data = $this->model::all();

        return $data;
    }

    public function gtmStore($request)
    {
        $gtm = $this->model::firstOrNew();

        if($gtm){
            $gtm->gtm_id = $request->gtm_id;
        }

        $gtm->save();

        return $gtm;
    }

    public function clarityStore($request)
    {
        $clarity = $this->model::firstOrNew();

        if($clarity){
            $clarity->clarity_id = $request->clarity_id;
        }

        $clarity->save();

        return $clarity;
    }

    public function pixelStore($request)
    {
        $pixel = $this->model::firstOrNew();

        if($pixel){
            $pixel->pixel_id = $request->pixel_id;
        }

        $pixel->save();

        return $pixel;
    }

    public function conversionStore($request)
    {
        $conversion = $this->model::firstOrNew();

        if($conversion){
            $conversion->pixel_api_token = $request->pixel_api_token;
        }

        $conversion->save();

        return $conversion;
    }

    public function analyticalStore($request)
    {
        $googleAnalytical = $this->model::firstOrNew();

        if($googleAnalytical){
            $googleAnalytical->ga4_measurement_id = $request->ga4_measurement_id;
        }

        $googleAnalytical->save();

        return $googleAnalytical;
    }

    public function eventStore($request)
    {
        $event = $this->model::firstOrNew();

        if($event){
            $event->test_event_code = $request->test_event_code;
        }

        $event->save();

        return $event;
    }
}
