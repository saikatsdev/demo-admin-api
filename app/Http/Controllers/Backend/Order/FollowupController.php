<?php

namespace App\Http\Controllers\Backend\Order;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Backend\Order\FollowupCollection;
use App\Repositories\Order\FollowupRepository;
use Illuminate\Http\Request;

class FollowupController extends BaseController
{
    public function __construct(protected FollowupRepository $repository){}

    public function index(Request $request)
    {
        $data = $this->repository->index($request);

        $data = new FollowupCollection($data);

        return $this->sendResponse($data, "Follow Up Order List", 200);
    }
}
