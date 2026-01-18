<?php

namespace App\Http\Controllers\Frontend\CMS;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;;
use App\Repositories\CMS\SliderRepository;
use App\Http\Resources\Frontend\CMS\SliderResource;
use App\Http\Resources\Frontend\CMS\SliderCollection;

class SliderController extends BaseController
{
    protected $repository;

    public function __construct(SliderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $sliders = $this->repository->index($request);

            $sliders = new SliderCollection($sliders);

            return $this->sendResponse($sliders, 'Slider list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $slider = $this->repository->show($id);

            $slider = new SliderResource($slider);

            return $this->sendResponse($slider, "Slider single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
