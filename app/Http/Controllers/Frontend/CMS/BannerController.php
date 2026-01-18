<?php

namespace App\Http\Controllers\Frontend\CMS;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\CMS\BannerRepository;
use App\Http\Resources\Frontend\CMS\BannerResource;
use App\Http\Resources\Frontend\CMS\BannerCollection;

class BannerController extends BaseController
{
    protected $repository;

    public function __construct(BannerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $banners = $this->repository->index($request);

            $banners = new BannerCollection($banners);

            return $this->sendResponse($banners, "Banner list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show($slug)
    {
        try {
            $status = StatusEnum::ACTIVE;
            $banner = $this->repository->show($slug, $status);

            $banner = new BannerResource($banner);

            return $this->sendResponse($banner, "Banner single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
