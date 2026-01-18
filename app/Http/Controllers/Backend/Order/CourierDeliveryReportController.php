<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\CourierDeliveryReportRepository;
use App\Http\Requests\Backend\Order\CourierDeliveryReportRequest;

class CourierDeliveryReportController extends BaseController
{
    public function __construct(protected CourierDeliveryReportRepository $repository) {}

    public function courierDeliveryReport(CourierDeliveryReportRequest $request)
    {
        // if (!$request->user()->hasPermission("orders-read")) {
        //     return $this->sendError(__("common.unauthorized"), 401);
        // }

        try {
            $courierDeliveryReport = $this->repository->courierDeliveryReport($request);

            return $this->sendResponse($courierDeliveryReport, "Courier delivery report", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}

