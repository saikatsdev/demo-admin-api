<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Repositories\MarketingToolRepository;
use Illuminate\Http\Request;

class MarketingToolController extends BaseController
{
    public function __construct(protected MarketingToolRepository $repository){}

    public function index(Request $request)
    {
        $data = $this->repository->index($request);

        return $this->sendResponse($data,"All Marketing Tools", 200);
    }
}
