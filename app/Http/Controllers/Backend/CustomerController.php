<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Controllers\BaseController;

class CustomerController extends BaseController
{
    public function __construct(protected UserRepository $repository) {}

    public function index(Request $request)
    {
        $customers = $this->repository->getCustomers($request);

        return $this->sendResponse($customers, 'Customer Summary Retrieved Successfully', 200);
    }

    public function getCustomerSummary()
    {
        $data = $this->repository->getCustomerSummary();

        return $this->sendResponse($data, 'Customer Summary Retrieved Successfully', 200);
    }
}
