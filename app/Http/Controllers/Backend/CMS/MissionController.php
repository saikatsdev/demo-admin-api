<?php

namespace App\Http\Controllers\Backend\CMS;

use App\Http\Resources\Backend\CMS\MissionCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Repositories\CMS\MissionRepository;
use App\Http\Resources\Backend\CMS\MissionResource;

class MissionController extends BaseController
{
    public function __construct(protected MissionRepository $repository){}

    public function index(Request $request)
    {
        $missions = $this->repository->index($request);

        $missions = new MissionCollection($missions);

        return $this->sendResponse($missions, "Mission List", 200);
    }

    public function store(Request $request)
    {
        $mission = $this->repository->store($request);

        $mission = new MissionResource($mission);

        return $this->sendResponse($mission, "Mission Created Successfully");
    }

    public function show($id)
    {
        $mission = $this->repository->show($id);

        $mission = new MissionResource($mission);

        return $this->sendResponse($mission, "Mission Show", 200);
    }

    public function update(Request $request, $id)
    {
        $mission = $this->repository->update($request, $id);

        $mission = new MissionResource($mission);

        return $this->sendResponse($mission, "Mission update successfully", 200);
    }

    public function destroy($id)
    {
        $mission = $this->repository->delete($id);

        return $this->sendResponse($mission, 'Mission deleted successfully', 200);
    }
}
