<?php

namespace App\Http\Controllers\Frontend\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Frontend\CMS\WarrantyPolicyCollection;
use App\Http\Resources\Frontend\CMS\WarrantyPolicyResource;
use App\Repositories\CMS\WarrantyPolicyRepository;
use Illuminate\Http\Request;

class WarrantyPolicyController extends BaseController
{
    public function __construct(protected WarrantyPolicyRepository $repository){}

    public function index(Request $request)
    {
        $policies = $this->repository->index($request);

        $policies = new WarrantyPolicyCollection($policies);

        return $this->sendResponse($policies, "Warranty Policies", 200);
    }

    public function show($id)
    {
        $policy = $this->repository->show($id);

        $policy = new WarrantyPolicyResource($policy);

        return $this->sendResponse($policy, "Warranty Policy", 200);
    }
}
