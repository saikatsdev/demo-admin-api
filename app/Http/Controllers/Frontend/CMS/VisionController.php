<?php

namespace App\Http\Controllers\Frontend\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Frontend\CMS\VisionCollection;
use App\Http\Resources\Frontend\CMS\VisionResource;
use App\Repositories\CMS\VisionRepository;
use Illuminate\Http\Request;

class VisionController extends BaseController
{
    public function __construct(protected VisionRepository $repository){}

    public function index(Request $request)
    {
        $policies = $this->repository->index($request);

        $policies = new VisionCollection($policies);

        return $this->sendResponse($policies, "Vision List", 200);
    }

    public function show($id)
    {
        $policy = $this->repository->show($id);

        $policy = new VisionResource($policy);

        return $this->sendResponse($policy, "Vision Show", 200);
    }
}
