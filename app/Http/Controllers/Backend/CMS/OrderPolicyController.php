<?php

namespace App\Http\Controllers\Backend\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Backend\CMS\OrderPolicyCollection;
use App\Http\Resources\Backend\CMS\OrderPolicyResource;
use App\Repositories\CMS\OrderPolicyRepository;
use Illuminate\Http\Request;

class OrderPolicyController extends BaseController
{
    public function __construct(protected OrderPolicyRepository $repository){}

    public function index(Request $request)
    {
        $policies = $this->repository->index($request);

        $policies = new OrderPolicyCollection($policies);

        return $this->sendResponse($policies, "Order Policy List", 200);
    }

    public function store(Request $request)
    {
        $policy = $this->repository->store($request);

        $policy = new OrderPolicyResource($policy);

        return $this->sendResponse($policy, "Order Policy Created Successfully");
    }

    public function show($id)
    {
        $policy = $this->repository->show($id);

        $policy = new OrderPolicyResource($policy);

        return $this->sendResponse($policy, "Order Policy Show", 200);
    }

    public function update(Request $request, $id)
    {
        $policy = $this->repository->update($request, $id);

        $policy = new OrderPolicyResource($policy);

        return $this->sendResponse($policy, "Order Policy update successfully", 200);
    }

    public function destroy($id)
    {
        $policy = $this->repository->delete($id);

        return $this->sendResponse($policy, 'Order Policy deleted successfully', 200);
    }
}
