<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\PathaoRepository;
use App\Http\Requests\Backend\Order\PathaoRequest;
use App\Http\Resources\Backend\Order\PathaoResource;
use App\Http\Resources\Backend\Order\PathaoAreaResource;
use App\Http\Requests\Backend\Order\PathaoCreateOrderRequest;
use App\Http\Requests\Backend\Order\PathaoCreateStoreRequest;
use App\Http\Requests\Backend\Order\PathaoBulkCreateOrderRequest;
use App\Http\Requests\Backend\Order\PathaoPriceCalculationRequest;

class PathaoController extends BaseController
{
    public function __construct(protected PathaoRepository $repository) {}

    public function getCity()
    {
        try {
            $result = $this->repository->getCities();

            return $this->sendResponse($result, 'City list');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getZone($cityId)
    {
        try {
            $result = $this->repository->getZones($cityId);

            return $this->sendResponse($result, 'Zone list');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getArea($zoneId)
    {
        try {
            $result = $this->repository->getAreas($zoneId);

            return $this->sendResponse($result, 'Pathao area list');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function getStore()
    {
        try {
            $result = $this->repository->getStores();

            return $this->sendResponse($result, 'Store list');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function createStore(PathaoCreateStoreRequest $request)
    {
        try {
            $result = $this->repository->createNewStore($request);

            if ($result['type'] == 'success') {
                return $this->sendResponse($result['message'], "Store created successfully");
            } else {
                $errorMessage = null;
                if (isset($result['errors']['name'])) {
                    $errorMessage = $result['errors']['name'][0];
                } elseif (isset($result['errors']['city_id'])) {
                    $errorMessage = $result['errors']['city_id'][0];
                } elseif (isset($result['errors']['zone_id'])) {
                    $errorMessage = $result['errors']['zone_id'][0];
                } elseif (isset($result['errors']['area_id'])) {
                    $errorMessage = $result['errors']['area_id'][0];
                } elseif (isset($result['errors']['contact_name'])) {
                    $errorMessage = $result['errors']['contact_name'][0];
                } elseif (isset($result['errors']['contact_number'])) {
                    $errorMessage = $result['errors']['contact_number'][0];
                } elseif (isset($result['errors']['address'])) {
                    $errorMessage = $result['errors']['address'][0];
                } else {
                    $errorMessage = 'Invalid information';
                }

                return $this->sendError($errorMessage);
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderShortInfo($consignmentId)
    {
        try {
            $data = $this->repository->orderShortInfo($consignmentId);

            return $this->sendResponse($data, null);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function createOrder(PathaoCreateOrderRequest $request)
    {
        try {
            $result = $this->repository->createOrder($request);

            return $this->sendResponse($result, 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function createBulkOrder(PathaoBulkCreateOrderRequest $request)
    {
        try {
            $result = $this->repository->createBulkOrder($request);

            return $this->sendResponse($result, 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function costCalculation(PathaoPriceCalculationRequest $request)
    {
        $result = $this->repository->priceCalculation($request);

        if ($result["type"] === 'error') {
            return $this->sendError($result);
        } else {
            return $this->sendResponse($result, 'Cost calculation');
        }
    }

    public function show(Request $request)
    {
        if (!$request->user()->hasPermission('pathao-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $pathao = $this->repository->show();

            $pathao = new PathaoResource($pathao);

            return $this->sendResponse($pathao, "Pathao credentials");
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function updateEnvCredential(PathaoRequest $request)
    {
        if (!$request->user()->hasPermission('pathao-update')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $res = $this->repository->updateEnvCredential($request);

            return $this->sendResponse($res, 'Credential updated successfully', 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function callback(Request $request)
    {
        try {
            $callback = $this->repository->callback($request);

            return $this->sendResponse($callback, 'Pathao callback response', 202);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function searchArea(Request $request)
    {
        try {
            $ares = $this->repository->searchArea($request);

            $ares = PathaoAreaResource::collection($ares);

            return $this->sendResponse($ares, 'Pathao area list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }
}
