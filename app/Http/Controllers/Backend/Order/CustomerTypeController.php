<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\CustomerTypeRepository;
use App\Http\Requests\Backend\Order\CustomerTypeRequest;
use App\Http\Resources\Backend\Order\CustomerTypeResource;
use App\Http\Resources\Backend\Order\CustomerTypeCollection;

class CustomerTypeController extends BaseController
{
    public function __construct(protected CustomerTypeRepository $repository) {}

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("customer-types-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $customerTypes = $this->repository->index($request);

            $customerTypes = new CustomerTypeCollection($customerTypes);

            return $this->sendResponse($customerTypes, "Customer type list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function list()
    {
        try {
            $customerTypes = $this->repository->list();

            return $this->sendResponse($customerTypes, "Customer type list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(CustomerTypeRequest $request)
    {
        if (!$request->user()->hasPermission("customer-types-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $customerType = $this->repository->store($request);

            $customerType = new CustomerTypeResource($customerType);

            return $this->sendResponse($customerType, "Customer type created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission("customer-types-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $customerType = $this->repository->show($id);

            $customerType = new customerTypeResource($customerType);

            return $this->sendResponse($customerType, "Customer type single view", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(CustomerTypeRequest $request, $id)
    {
        if (!$request->user()->hasPermission("customer-types-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $customerType = $this->repository->update($request, $id);

            $customerType = new CustomerTypeResource($customerType);

            return $this->sendResponse($customerType, "Customer type updated successfully", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("customer-types-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $customerType = $this->repository->delete($id);

            return $this->sendResponse($customerType, "Customer type deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
