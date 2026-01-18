<?php

namespace App\Http\Controllers\Frontend\CMS;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\CMS\AboutRepository;
use App\Http\Resources\Frontend\CMS\AboutResource;
use App\Http\Resources\Frontend\CMS\AboutCollection;

class AboutController extends BaseController
{
    protected $repository;

    public function __construct(AboutRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $abouts = $this->repository->index($request);

            $abouts = new AboutCollection($abouts);

            return $this->sendResponse($abouts, "About list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $about = $this->repository->show($id);

            $about = new AboutResource($about);

            return $this->sendResponse($about, "About single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
