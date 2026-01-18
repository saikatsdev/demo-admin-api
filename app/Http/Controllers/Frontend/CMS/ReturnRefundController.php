<?php

namespace App\Http\Controllers\Frontend\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Frontend\CMS\ReturnPolicyCollection;
use App\Http\Resources\Frontend\CMS\ReturnPolicyResource;
use App\Repositories\CMS\ReturnPolicyRepository;
use Illuminate\Http\Request;

class ReturnRefundController extends BaseController
{
    public function __construct(protected ReturnPolicyRepository $repository){}

    public function index(Request $request)
    {
        $policies = $this->repository->index($request);

        $policies = new ReturnPolicyCollection($policies);

        return $this->sendResponse($policies, "Return & Refund Policies", 200);
    }

    public function show($id)
    {
        $policy = $this->repository->show($id);

        $policy = new ReturnPolicyResource($policy);

        return $this->sendResponse($policy, "Return & Refund Policy", 200);
    }
}
