<?php

namespace App\Http\Controllers\Backend\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Backend\CMS\WarrantyPolicyCollection;
use App\Http\Resources\Backend\CMS\WarrantyPolicyResource;
use App\Repositories\CMS\WarrantyPolicyRepository;
use Illuminate\Http\Request;

class WarrantyPolicyController extends BaseController
{
    public function __construct(protected WarrantyPolicyRepository $repository){}

    public function index(Request $request)
    {
        $policies = $this->repository->index($request);

        $policies = new WarrantyPolicyCollection($policies);

        return $this->sendResponse($policies, "Warranty Policy List", 200);
    }

    public function store(Request $request)
    {
        $policy = $this->repository->store($request);

        $policy = new WarrantyPolicyResource($policy);

        return $this->sendResponse($policy, "Warranty Policy Created Successfully");
    }

    public function show($id)
    {
        $policy = $this->repository->show($id);

        $policy = new WarrantyPolicyResource($policy);

        return $this->sendResponse($policy, "Warranty Policy Show", 200);
    }

    public function update(Request $request, $id)
    {
        $policy = $this->repository->update($request, $id);

        $policy = new WarrantyPolicyResource($policy);

        return $this->sendResponse($policy, "Warranty Policy update successfully", 200);
    }

    public function destroy($id)
    {
        $policy = $this->repository->delete($id);

        return $this->sendResponse($policy, 'Warranty Policy deleted successfully', 200);
    }
}
