<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BaseController;
use App\Repositories\MarketingToolRepository;
use Illuminate\Http\Request;

class MarketingToolController extends BaseController
{
    public function __construct(protected MarketingToolRepository $repository){}

    public function index(Request $request)
    {
        $data = $this->repository->index($request);

        return $this->sendResponse($data, "All Tools", 200);
    }

    public function gtmStore(Request $request)
    {
        $data = $this->repository->gtmStore($request);

        return $this->sendResponse($data, "Gtm Id Update Successfully");
    }

    public function clarityStore(Request $request)
    {
        $data = $this->repository->clarityStore($request);

        return $this->sendResponse($data, "Clarity Id Update Successfully");
    }

    public function pixelStore(Request $request)
    {
        $data = $this->repository->pixelStore($request);

        return $this->sendResponse($data, "Pixel Id Update Successfully");
    }

    public function conversionStore(Request $request)
    {
        $data = $this->repository->conversionStore($request);

        return $this->sendResponse($data, "Conversion Token Update Successfully");
    }

    public function analyticalStore(Request $request)
    {
        $data = $this->repository->analyticalStore($request);

        return $this->sendResponse($data, "Analytical Token Update Successfully");
    }

    public function eventStore(Request $request)
    {
        $data = $this->repository->eventStore($request);

        return $this->sendResponse($data, "Event Test Code Update Successfully");
    }
}
