<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\SettingRepository;
use App\Http\Resources\Frontend\SettingResource;
use App\Http\Resources\Frontend\SettingCollection;

class SettingController extends BaseController
{
    protected $repository;

    public function __construct(SettingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $settings = $this->repository->list($request);

            $settings = SettingResource::collection($settings);

            return $this->sendResponse($settings, "Setting list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $setting = $this->repository->show($id);

            $setting = new SettingResource($setting);

            return $this->sendResponse($setting, "Setting single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function isModuleActive(Request $request)
    {
        try {
            $setting = $this->repository->isModuleActive($request);

            return $this->sendResponse($setting, "Module status", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }
}
