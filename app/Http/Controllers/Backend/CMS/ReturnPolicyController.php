<?php

namespace App\Http\Controllers\Backend\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Backend\CMS\ReturnPolicyCollection;
use App\Http\Resources\Backend\CMS\ReturnPolicyResource;
use App\Repositories\CMS\ReturnPolicyRepository;
use Illuminate\Http\Request;

class ReturnPolicyController extends BaseController
{
    public function __construct(protected ReturnPolicyRepository $repository){}

    public function index(Request $request)
    {
        $policies = $this->repository->index($request);

        $policies = new ReturnPolicyCollection($policies);

        return $this->sendResponse($policies, "Return Policy List", 200);
    }

    public function store(Request $request)
    {
        $policy = $this->repository->store($request);

        $policy = new ReturnPolicyResource($policy);

        return $this->sendResponse($policy, "Return Policy Created Successfully");
    }

    public function show($id)
    {
        $policy = $this->repository->show($id);

        $policy = new ReturnPolicyResource($policy);

        return $this->sendResponse($policy, "Return Policy Show", 200);
    }

    public function update(Request $request, $id)
    {
        $policy = $this->repository->update($request, $id);

        $policy = new ReturnPolicyResource($policy);

        return $this->sendResponse($policy, "Return Policy update successfully", 200);
    }

    public function destroy($id)
    {
        $policy = $this->repository->delete($id);

        return $this->sendResponse($policy, 'Return Policy deleted successfully', 200);
    }
}
