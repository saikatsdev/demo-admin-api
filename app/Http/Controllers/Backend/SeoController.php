<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\SeoRequest;
use App\Http\Resources\Backend\SeoCollection;
use App\Http\Resources\Backend\SeoResource;
use App\Repositories\SeoRepository;
use Illuminate\Http\Request;

class SeoController extends BaseController
{
    public function __construct(protected SeoRepository $repository){}

    public function index(Request $request)
    {
        $data = $this->repository->index($request);

        $data = new SeoCollection($data);

        return $this->sendResponse($data, "SEO list.", 200);
    }

    public function store(SeoRequest $request)
    {
        $data = $this->repository->store($request);

        $data = new SeoResource($data);

        return $this->sendResponse($data, "SEO created successfully.", 200);
    }

    public function show($id)
    {
        $data = $this->repository->show($id);

        $data = new SeoResource($data);

        return $this->sendResponse($data, "SEO details retrieved successfully.", 200);
    }

    public function update(SeoRequest $request, $id)
    {
        $data = $this->repository->update($request, $id);

        $data = new SeoResource($data);

        return $this->sendResponse($data, "SEO updated successfully.", 200);
    }

    public function destroy($id)
    {
        $data = $this->repository->destroy($id);

        return $this->sendResponse($data, "SEO deleted successfully.", 200);
    }
}