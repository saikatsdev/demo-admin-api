<?php

namespace App\Http\Controllers\Frontend\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Frontend\CMS\ShippingPolicyCollection;
use App\Http\Resources\Frontend\CMS\ShippingPolicyResource;
use App\Repositories\CMS\ShippingPolicyRepository;
use Illuminate\Http\Request;

class ShippingPolicyController extends BaseController
{
    public function __construct(protected ShippingPolicyRepository $repository){}

    public function index(Request $request)
    {
        $policies = $this->repository->index($request);

        $policies = new ShippingPolicyCollection($policies);

        return $this->sendResponse($policies, "Shipping Policies", 200);
    }

    public function show($id)
    {
        $policy = $this->repository->show($id);

        $policy = new ShippingPolicyResource($policy);

        return $this->sendResponse($policy, "Shipping Policy", 200);
    }
}
