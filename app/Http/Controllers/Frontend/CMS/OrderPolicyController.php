<?php

namespace App\Http\Controllers\Frontend\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Frontend\CMS\OrderPolicyCollection;
use App\Http\Resources\Frontend\CMS\OrderPolicyResource;
use App\Repositories\CMS\OrderPolicyRepository;
use Illuminate\Http\Request;

class OrderPolicyController extends BaseController
{
    public function __construct(protected OrderPolicyRepository $repository){}

    public function index(Request $request)
    {
        $policies = $this->repository->index($request);

        $policies = new OrderPolicyCollection($policies);

        return $this->sendResponse($policies, "Order Policies", 200);
    }

    public function show($id)
    {
        $policy = $this->repository->show($id);

        $policy = new OrderPolicyResource($policy);

        return $this->sendResponse($policy, "Order Policy", 200);
    }
}
