<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Models\Order\OrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order\IncompleteOrder;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function dashboard(Request $request)
    {
        if (!$request->user()->hasPermission('dashboards-read')) {
            return $this->sendError(__("Common.unauthorized"));
        }

        $today = Carbon::today();
        $now   = Carbon::now();

        $incompleteOrders = IncompleteOrder::select(DB::raw('DATE(created_at) as date'),DB::raw('COUNT(*) as orders'))->groupBy('date')->orderBy('date', 'ASC')->get();

        $orderByStatus = Order::select('orders.current_status_id as id','statuses.name',DB::raw('COUNT(*) as total_orders'))
        ->join('statuses', 'statuses.id', '=', 'orders.current_status_id')
        ->groupBy('orders.current_status_id', 'statuses.name')
        ->orderBy('orders.current_status_id')
        ->get();


        $totalUsers = User::count();

        $ordersStats = Order::select(DB::raw('
            COUNT(*) as total_orders,
            SUM(payable_price) as all_time_sales,
            SUM(CASE WHEN current_status_id = 8 THEN payable_price ELSE 0 END) as all_time_cancel,
            SUM(CASE WHEN current_status_id = 10 THEN payable_price ELSE 0 END) as all_time_returned,

            SUM(CASE WHEN DATE(created_at) = "' . $today . '" THEN 1 ELSE 0 END) as today_orders,
            SUM(CASE WHEN DATE(created_at) = "' . $today . '" THEN payable_price ELSE 0 END) as today_sales,
            SUM(CASE WHEN DATE(created_at) = "' . $today . '" AND current_status_id = 8 THEN payable_price ELSE 0 END) as today_cancel,
            SUM(CASE WHEN DATE(created_at) = "' . $today . '" AND current_status_id = 10 THEN payable_price ELSE 0 END) as today_returned,

            SUM(CASE WHEN MONTH(created_at) = ' . $now->month . ' AND YEAR(created_at) = ' . $now->year . ' THEN 1 ELSE 0 END) as this_month_orders,
            SUM(CASE WHEN MONTH(created_at) = ' . $now->month . ' AND YEAR(created_at) = ' . $now->year . ' THEN payable_price ELSE 0 END) as this_month_sales,
            SUM(CASE WHEN MONTH(created_at) = ' . $now->month . ' AND YEAR(created_at) = ' . $now->year . ' AND current_status_id = 8 THEN payable_price ELSE 0 END) as this_month_cancel,
            SUM(CASE WHEN MONTH(created_at) = ' . $now->month . ' AND YEAR(created_at) = ' . $now->year . ' AND current_status_id = 10 THEN payable_price ELSE 0 END) as this_month_returned,

            SUM(CASE WHEN YEAR(created_at) = ' . $now->year . ' THEN 1 ELSE 0 END) as this_year_orders,
            SUM(CASE WHEN YEAR(created_at) = ' . $now->year . ' THEN payable_price ELSE 0 END) as this_year_sales,
            SUM(CASE WHEN YEAR(created_at) = ' . $now->year . ' AND current_status_id = 8 THEN payable_price ELSE 0 END) as this_year_cancel,
            SUM(CASE WHEN YEAR(created_at) = ' . $now->year . ' AND current_status_id = 10 THEN payable_price ELSE 0 END) as this_year_returned
        '))->first();

        $orderReport = Order::select(DB::raw('
            COUNT(*) as order_count,
            SUM(payable_price) as order_value,
            SUM(CASE WHEN paid_status = "paid"   THEN 1 ELSE 0 END) as paid_order,
            SUM(CASE WHEN paid_status = "paid"   THEN payable_price ELSE 0 END) as paid_order_value,
            SUM(CASE WHEN paid_status = "unpaid" THEN 1 ELSE 0 END) as unpaid_order,
            SUM(CASE WHEN paid_status = "unpaid" THEN payable_price ELSE 0 END) as unpaid_order_value,
            SUM(CASE WHEN current_status_id = 1 THEN 1 ELSE 0 END) as submitted_order,
            SUM(CASE WHEN current_status_id = 1 THEN payable_price ELSE 0 END) as submitted_order_value,
            SUM(CASE WHEN current_status_id = 3 THEN 1 ELSE 0 END) as confirm_order,
            SUM(CASE WHEN current_status_id = 3 THEN payable_price ELSE 0 END) as confirm_order_value,
            SUM(CASE WHEN current_status_id = 5 THEN 1 ELSE 0 END) as canceled_order,
            SUM(CASE WHEN current_status_id = 5 THEN payable_price ELSE 0 END) as canceled_order_value,
            SUM(CASE WHEN current_status_id = 8 THEN 1 ELSE 0 END) as delivered_order,
            SUM(CASE WHEN current_status_id = 8 THEN payable_price ELSE 0 END) as delivered_order_value,
            SUM(CASE WHEN current_status_id = 9 THEN 1 ELSE 0 END) as returned_order,
            SUM(CASE WHEN current_status_id = 9 THEN payable_price ELSE 0 END) as returned_order_value
        '))->first();

        $upSellStats = OrderDetail::select(DB::raw('
            SUM(CASE WHEN is_upsell = 1 THEN (sell_price * quantity) ELSE 0 END) as all_time_upsell,

            SUM(CASE WHEN is_upsell = 1 AND DATE(created_at) = "' . $today . '"
                THEN (sell_price * quantity) ELSE 0 END) as today_upsell,

            SUM(CASE WHEN is_upsell = 1
                AND MONTH(created_at) = ' . $now->month . '
                AND YEAR(created_at) = ' . $now->year . '
                THEN (sell_price * quantity) ELSE 0 END) as this_month_upsell,

            SUM(CASE WHEN is_upsell = 1
                AND YEAR(created_at) = ' . $now->year . '
                THEN (sell_price * quantity) ELSE 0 END) as this_year_upsell
        '))->first();

        $followUpStats = Order::where('is_follow_order', 1)
        ->select(DB::raw('
            SUM(payable_price) as all_time_amount,

            SUM(CASE
                WHEN DATE(created_at) = "' . $today . '"
                THEN payable_price ELSE 0
            END) as today_amount,

            SUM(CASE
                WHEN MONTH(created_at) = ' . $now->month . '
                AND YEAR(created_at) = ' . $now->year . '
                THEN payable_price ELSE 0
            END) as this_month_amount,

            SUM(CASE
                WHEN YEAR(created_at) = ' . $now->year . '
                THEN payable_price ELSE 0
            END) as this_year_amount
        '))->first();

        $downSellStats = Order::where('is_down_sell', 1)
        ->select(DB::raw('
            SUM(payable_price) as all_time_amount,

            SUM(CASE
                WHEN DATE(created_at) = "' . $today . '"
                THEN payable_price ELSE 0
            END) as today_amount,

            SUM(CASE
                WHEN MONTH(created_at) = ' . $now->month . '
                AND YEAR(created_at) = ' . $now->year . '
                THEN payable_price ELSE 0
            END) as this_month_amount,

            SUM(CASE
                WHEN YEAR(created_at) = ' . $now->year . '
                THEN payable_price ELSE 0
            END) as this_year_amount
        '))
        ->first();

        $data = [
            'total_users'  => $totalUsers,
            'today_orders' => $ordersStats->today_orders,
            'this_month_orders' => $ordersStats->this_month_orders,
            'this_year_orders' => $ordersStats->this_year_orders,

            'today_sales' => $ordersStats->today_sales,
            'this_month_sales' => $ordersStats->this_month_sales,
            'this_year_sales' => $ordersStats->this_year_sales,
            'all_time_sales' => $ordersStats->all_time_sales,

            'today_cancel' => $ordersStats->today_cancel,
            'this_month_cancel' => $ordersStats->this_month_cancel,
            'this_year_cancel' => $ordersStats->this_year_cancel,
            'all_time_cancel' => $ordersStats->all_time_cancel,

            'today_returned' => $ordersStats->today_returned,
            'this_month_returned' => $ordersStats->this_month_returned,
            'this_year_returned' => $ordersStats->this_year_returned,
            'all_time_returned' => $ordersStats->all_time_returned,

            'incompleteOrders' => $incompleteOrders,
            'orderByStatus' => $orderByStatus,
            'order_report' => $orderReport,

            'upsell_today'      => $upSellStats->today_upsell,
            'upsell_this_month' => $upSellStats->this_month_upsell,
            'upsell_this_year'  => $upSellStats->this_year_upsell,
            'upsell_all_time'   => $upSellStats->all_time_upsell,

            'followup_today'      => (float) $followUpStats->today_amount,
            'followup_this_month' => (float) $followUpStats->this_month_amount,
            'followup_this_year'  => (float) $followUpStats->this_year_amount,
            'followup_all_time'   => (float) $followUpStats->all_time_amount,

            'downsell_today'      => (float) $downSellStats->today_amount,
            'downsell_this_month' => (float) $downSellStats->this_month_amount,
            'downsell_this_year'  => (float) $downSellStats->this_year_amount,
            'downsell_all_time'   => (float) $downSellStats->all_time_amount,
        ];

        return $this->sendResponse($data, 'Dashboard information');
    }


    public function cacheClear()
    {
        try {
            Artisan::call('optimize:clear');

            return $this->sendResponse([], 'Successfully cache clear');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }
}
