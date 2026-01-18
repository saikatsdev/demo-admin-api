<?php

namespace App\Http\Controllers\Backend\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Backend\CMS\ShippingPolicyCollection;
use App\Http\Resources\Backend\CMS\ShippingPolicyResource;
use App\Repositories\CMS\ShippingPolicyRepository;
use Illuminate\Http\Request;

class ShippingPolicyController extends BaseController
{
    public function __construct(protected ShippingPolicyRepository $repository){}

    public function index(Request $request)
    {
        $policies = $this->repository->index($request);

        $policies = new ShippingPolicyCollection($policies);

        return $this->sendResponse($policies, "Shipping Policy List", 200);
    }

    public function store(Request $request)
    {
        $policy = $this->repository->store($request);

        $policy = new ShippingPolicyResource($policy);

        return $this->sendResponse($policy, "Shipping Policy Created Successfully");
    }

    public function show($id)
    {
        $policy = $this->repository->show($id);

        $policy = new ShippingPolicyResource($policy);

        return $this->sendResponse($policy, "Shipping Policy Show", 200);
    }

    public function update(Request $request, $id)
    {
        $policy = $this->repository->update($request, $id);

        $policy = new ShippingPolicyResource($policy);

        return $this->sendResponse($policy, "Shipping Policy update successfully", 200);
    }

    public function destroy($id)
    {
        $policy = $this->repository->delete($id);

        return $this->sendResponse($policy, 'Shipping Policy deleted successfully', 200);
    }
}
