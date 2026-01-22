<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use App\Repositories\ReportRepository;
use App\Http\Requests\Backend\OrderReportRequest;

class ReportController extends BaseController
{
    public function __construct(protected ReportRepository $repository){}

    public function orderReport(OrderReportRequest $request)
    {
        if (!$request->user()->hasPermission("reports-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->orderReport($request);

            return $this->sendResponse($orders, "Order report", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderReportMonthly(Request $request)
    {
        if (!$request->user()->hasPermission("reports-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->orderReportMonthly();

            return $this->sendResponse($orders, "Monthly order report", 200);

        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderReportYearly(Request $request)
    {
        if (!$request->user()->hasPermission("reports-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders  = $this->repository->orderReportYearly();

            return $this->sendResponse($orders, "Yearly order report", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderReportByLocation(Request $request)
    {
        if (!$request->user()->hasPermission("reports-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->orderReportByLocation($request);

            return $this->sendResponse($orders, "Order report by location", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderReportBySelling(Request $request)
    {
        if (!$request->user()->hasPermission("reports-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $products = $this->repository->orderReportBySelling($request);

            return $this->sendResponse($products, "Top selling products", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderReportByCustomer(Request $request)
    {
        if (!$request->user()->hasPermission("reports-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orders = $this->repository->orderReportByCustomer($request);

            return $this->sendResponse($orders, "Order report by customer", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderProfitSummaryReport(Request $request)
    {
        if (!$request->user()->hasPermission("reports-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orderProfitSummaryReport = $this->repository->orderProfitSummaryReport();

            return $this->sendResponse($orderProfitSummaryReport, "Order profit summary report", 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderProfitReport(Request $request)
    {
        if (!$request->user()->hasPermission("reports-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orderProfitReport = $this->repository->orderProfitReport($request);

            return $this->sendResponse($orderProfitReport, "Order profit report", 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderCancelReport(Request $request)
    {
        if (!$request->user()->hasPermission("reports-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orderCancelReport = $this->repository->orderCancelReport($request);

            return $this->sendResponse($orderCancelReport, "Cancel Order report", 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderReturnReport(Request $request)
    {
        if (!$request->user()->hasPermission("reports-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $returnOrders = $this->repository->orderReturnReport($request);

            return $this->sendResponse($returnOrders, "Return Order report", 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function incompleteOrderReport(Request $request)
    {
        if (!$request->user()->hasPermission("reports-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $incompleteOrderReport = $this->repository->incompleteOrderReport($request);

            return $this->sendResponse($incompleteOrderReport, "incomplete orders report", 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function getDownSellReport(Request $request)
    {
        $data = $this->repository->getDownSellReport($request);

        return $this->sendResponse($data, "Down Sell Report", 200);
    }

    public function getLowestProducts(Request $request)
    {
        $data = $this->repository->getLowestProducts($request);

        return $this->sendResponse($data, "Lowest Stock Product", 200);
    }

    public function downsellReport(Request $request)
    {
        try {
            $downSellReports = $this->repository->downsellReport($request);

            return $this->sendResponse($downSellReports, "Down Sell Report", 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function followUpReport(Request $request)
    {
        try {
            $followUpOrders = $this->repository->followUpReport($request);

            return $this->sendResponse($followUpOrders, "Follow Up Report", 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }
}
