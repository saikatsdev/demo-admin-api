<?php

namespace App\Http\Controllers\Frontend\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Frontend\CMS\MissionCollection;
use App\Http\Resources\Frontend\CMS\MissionResource;
use App\Repositories\CMS\MissionRepository;
use Illuminate\Http\Request;

class MissionController extends BaseController
{
    public function __construct(protected MissionRepository $repository){}

    public function index(Request $request)
    {
        $policies = $this->repository->index($request);

        $policies = new MissionCollection($policies);

        return $this->sendResponse($policies, "Mission List", 200);
    }

    public function show($id)
    {
        $policy = $this->repository->show($id);

        $policy = new MissionResource($policy);

        return $this->sendResponse($policy, "Mission Show", 200);
    }
}
