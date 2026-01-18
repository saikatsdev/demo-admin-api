<?php

namespace App\Http\Controllers\Frontend\Product;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Frontend\Product\UpSellSettingResource;
use App\Repositories\Product\UpSellSettingRepository;
use Illuminate\Http\Request;

class UpSellSettingController extends BaseController
{
    public function __construct(protected UpSellSettingRepository $repository){}

    public function index(Request $request)
    {
        $setting = $this->repository->index($request);

        $setting = new UpSellSettingResource($setting);

        return $this->sendResponse($setting, "Up Sell Setting");
    }
}
