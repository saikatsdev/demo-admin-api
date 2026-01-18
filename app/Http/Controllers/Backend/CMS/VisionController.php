<?php

namespace App\Http\Controllers\Backend\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Backend\CMS\VisionCollection;
use App\Http\Resources\Backend\CMS\VisionResource;
use App\Repositories\CMS\VisionRepository;
use Illuminate\Http\Request;

class VisionController extends BaseController
{
    public function __construct(protected VisionRepository $repository){}

    public function index(Request $request)
    {
        $visions = $this->repository->index($request);

        $visions = new VisionCollection($visions);

        return $this->sendResponse($visions, "Vision List", 200);
    }

    public function store(Request $request)
    {
        $vision = $this->repository->store($request);

        $vision = new VisionResource($vision);

        return $this->sendResponse($vision, "Vision Created Successfully");
    }

    public function show($id)
    {
        $vision = $this->repository->show($id);

        $vision = new VisionResource($vision);

        return $this->sendResponse($vision, "Vision Show", 200);
    }

    public function update(Request $request, $id)
    {
        $vision = $this->repository->update($request, $id);

        $vision = new VisionResource($vision);

        return $this->sendResponse($vision, "Vision update successfully", 200);
    }

    public function destroy($id)
    {
        $vision = $this->repository->delete($id);

        return $this->sendResponse($vision, 'Vision deleted successfully', 200);
    }
}
