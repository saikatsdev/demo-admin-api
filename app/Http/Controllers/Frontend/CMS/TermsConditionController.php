<?php

namespace App\Http\Controllers\Frontend\CMS;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Repositories\CMS\TermsAndConditionRepository;

class TermsConditionController extends BaseController
{
    public function __construct(protected TermsAndConditionRepository $repository){}

    public function index(Request $request)
    {
        $data = $this->repository->index($request);

        return $this->sendResponse($data,"Terms & Condition", 200);
    }
}
