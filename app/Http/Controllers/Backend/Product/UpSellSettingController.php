<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Backend\Product\UpSellSettingRequest;
use App\Http\Resources\Backend\Product\UpSellSettingResource;
use App\Repositories\Product\UpSellSettingRepository;
use Illuminate\Http\Request;

class UpSellSettingController extends BaseController
{
    public function __construct(protected UpSellSettingRepository $repository){}

    public function index(Request $request)
    {
        $setting = $this->repository->index($request);

        $setting = new UpSellSettingResource($setting);

        return $this->sendResponse($setting, "UpSell Setting");
    }

    public function update(UpSellSettingRequest $request)
    {
        $setting = $this->repository->update($request);

        $setting = new UpSellSettingResource($setting);

        return $this->sendResponse($setting, "UpSell Setting updated successfully");
    }
}
