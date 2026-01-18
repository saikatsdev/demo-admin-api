<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\Order;
use App\Models\Order\OrderDetail;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Entities\Expense;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\StockManagement\Entities\Purchase;
use Modules\StockManagement\Entities\Supplier;

class ReportRepository
{
    public function __construct(protected Order $model) {}

    public function orderReport($request)
    {
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);
        $orderFromId  = $request->input("order_from_id", null);
        $paginateSize = Helper::checkPaginateSize($request);

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate   = Carbon::parse($endDate)->endOfDay();
        }

        $orders = $this->model
            ->select("id", "current_status_id", "order_from_id", "invoice_number", "payable_price", "paid_status", "phone_number", "created_at")
            ->with(["currentStatus:id,name", "orderFrom:id,name"])
            ->withSum("details as order_quantity", "quantity")
            ->when($orderFromId, fn($query) => $query->where("order_from_id", $orderFromId))
            ->when($startDate && $endDate, fn($query) => $query->whereBetween("created_at", [$startDate, $endDate]))
            ->orderBy("created_at", "DESC")
            ->paginate($paginateSize);

        return [
            "total_order"    => $orders->count(),
            "total_amount"   => $orders->sum("payable_price"),
            "total_quantity" => $orders->sum("order_quantity"),
            "orders"         => $orders,
        ];
    }

    public function orderReportMonthly()
    {
        $currentMonth = Carbon::now()->format("F");
        $daysInMonth  = Carbon::now()->daysInMonth;

        $orders = $this->model->select(
            DB::raw("GROUP_CONCAT(DATE(created_at)) as order_dates"),
            DB::raw("COUNT(id) as order_count")
        )
            ->whereMonth("created_at", "=", Carbon::now()->month)
            ->orderBy("order_dates")
            ->get();

        // Explode the concatenated string into an array
        $orderDatesArray = explode(",", $orders->first()->order_dates);

        // Create an array with days of the month and initialize order counts to 0
        $daysArray = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $daysArray[$day] = 0;
        }

        // Fill in the order counts for each day
        foreach ($orderDatesArray as $orderDate) {
            $day = Carbon::parse($orderDate)->format("j"); // Format day as a string
            $daysArray[$day] += 1; // Increment the count for the specific day
        }

        // Output the result
        $result = [];
        foreach ($daysArray as $orderDate => $orderCount) {
            $result[] = [
                "order_date"  => $orderDate,
                "order_count" => $orderCount,
            ];
        }

        $data = [
            "current_month" => $currentMonth,
            "data"          => $result
        ];

        return $data;
    }

    public function orderReportYearly()
    {
        $currentYear = Carbon::now()->year;

        $orders = $this->model->select(
            DB::raw("MONTH(created_at) as month"),
            DB::raw("COUNT(id) as order_count")
        )
            ->whereYear("created_at", "=", $currentYear)
            ->groupBy("month")
            ->orderBy("month")
            ->get();

        // Initialize an array with counts for each month
        $monthsArray = array_fill(1, 12, 0);

        // Fill in the order counts for each month
        foreach ($orders as $orderCount) {
            $month = $orderCount->month;
            $count = $orderCount->order_count;
            $monthsArray[$month] = $count;
        }

        // Output the result
        $result = [];
        foreach ($monthsArray as $month => $orderCount) {
            $result[] = [
                "month" => date("F", mktime(0, 0, 0, $month, 1)), // Format month as full month name
                "order_count" => $orderCount,
            ];
        }

        $data = [
            "current_year" => $currentYear,
            "data" => $result
        ];

        return $data;
    }

    public function orderReportByLocation($request)
    {
        $limit = $request->input("limit", 10);

        $orders = $this->model->select(
            "orders.district_id",
            "districts.name as district_name",
            DB::raw("COUNT(orders.id) as order_count")
        )
            ->join("districts", "orders.district_id", "=", "districts.id")
            ->groupBy("orders.district_id", "districts.name")
            ->take($limit)
            ->get();

        return $orders;
    }

    public function orderReportBySelling($request)
    {
        $limit = $request->input("limit", 10);

        $products = $this->model->select(
            "products.id",
            "products.name",
            "products.buy_price",
            "products.mrp",
            "products.offer_price",
            "products.discount",
            "products.sell_price",
            "products.current_stock",
            "products.img_path",
            DB::raw("COUNT(orders.id) as order_count"),
            DB::raw("
            CASE
                WHEN COUNT(product_variations.id) > 0
                THEN MIN(product_variations.sell_price)
                ELSE NULL
            END as min_sell_price
        "),
            DB::raw("
            CASE
                WHEN COUNT(product_variations.id) > 0
                THEN MAX(product_variations.sell_price)
                ELSE NULL
            END as max_sell_price
        ")
        )
            ->join("order_details", "orders.id", "=", "order_details.order_id")
            ->join("products", "order_details.product_id", "=", "products.id")
            ->leftJoin("product_variations", "products.id", "=", "product_variations.product_id")
            ->groupBy(
                "products.id",
                "products.name",
                "products.buy_price",
                "products.mrp",
                "products.offer_price",
                "products.discount",
                "products.sell_price",
                "products.current_stock",
                "products.img_path"
            )
            ->take($limit)
            ->get()
            ->map(function ($item) {
                return [
                    "id"             => $item->id,
                    "name"           => $item->name,
                    "buy_price"      => $item->buy_price,
                    "mrp"            => $item->mrp,
                    "offer_price"    => $item->offer_price,
                    "discount"       => $item->discount,
                    "sell_price"     => $item->sell_price,
                    "current_stock"  => $item->current_stock,
                    "img_path"       => Helper::getFilePath($item->img_path),
                    "order_count"    => $item->order_count,
                    "min_sell_price" => $item->min_sell_price,
                    "max_sell_price" => $item->max_sell_price
                ];
            });

        return $products;
    }

    public function orderReportBySellingFrontend($request)
    {
        $limit = $request->input("limit", 10);

        $products = Product::select(
            "products.id",
            "products.brand_id",
            "products.category_id",
            "products.sub_category_id",
            "products.name",
            "products.slug",
            "products.buy_price",
            "products.mrp",
            "products.offer_price",
            "products.discount",
            "products.sell_price",
            "products.current_stock",
            "products.img_path",
            DB::raw("COUNT(orders.id) as order_count"),
            DB::raw("MIN(product_variations.sell_price) as min_sell_price"),
            DB::raw("MAX(product_variations.sell_price) as max_sell_price")
        )
            ->join("order_details", "order_details.product_id", "=", "products.id")
            ->join("orders", "orders.id", "=", "order_details.order_id")
            ->leftJoin("product_variations", "products.id", "=", "product_variations.product_id")
            ->groupBy(
                "products.id",
                "products.brand_id",
                "products.category_id",
                "products.sub_category_id",
                "products.name",
                "products.slug",
                "products.buy_price",
                "products.mrp",
                "products.offer_price",
                "products.discount",
                "products.sell_price",
                "products.current_stock",
                "products.img_path"
            )
            ->with([
                "category:id,name,slug",
                "brand:id,name,slug",
                "subCategory:id,name,slug",
                "subSubCategory:id,name,slug",
                "images" => function ($query) {
                    $query->limit(1);
                },
                "variations",
                "variations.attributeValue1:id,value,attribute_id",
                "variations.attributeValue2:id,value,attribute_id",
                "variations.attributeValue3:id,value,attribute_id",
                "variations.attributeValue1.attribute:id,name",
                "variations.attributeValue2.attribute:id,name",
                "variations.attributeValue3.attribute:id,name"
            ])
            ->orderByDesc('order_count')
            ->take($limit)
            ->get();

        return $products;
    }

    public function orderReportByCustomer($request)
    {
        $limit = $request->input("limit", 10);

        $orders = $this->model->select("customer_name", "phone_number", DB::raw("COUNT(id) as order_count"), DB::raw("SUM(net_order_price) as order_value"))
            ->groupBy("customer_name", "phone_number")
            ->take($limit)
            ->get();

        return $orders;
    }

    public function orderProfitSummaryReport()
    {
        $data["today_profit"] = $this->model
            ->today()
            ->where("paid_status", StatusEnum::PAID)
            ->selectRaw("SUM(net_order_price - buy_price) as today_profit")
            ->value("today_profit") ?? 0;

        $data["monthly_profit"] = $this->model
            ->thisMonth()
            ->where("paid_status", StatusEnum::PAID)
            ->selectRaw("SUM(net_order_price - buy_price) as monthly_profit")
            ->value("monthly_profit") ?? 0;

        $data["yearly_profit"] = $this->model
            ->thisYear()
            ->where("paid_status", StatusEnum::PAID)
            ->selectRaw("SUM(net_order_price - buy_price) as yearly_profit")
            ->value("yearly_profit") ?? 0;

        $data["all_profit"] = $this->model
            ->where("paid_status", StatusEnum::PAID)
            ->selectRaw("SUM(net_order_price - buy_price) as all_profit")
            ->value("all_profit") ?? 0;

        return $data;
    }

    public function orderProfitReport($request)
    {
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);
        $orderFromId  = $request->input("order_from_id", null);
        $paginateSize = Helper::checkPaginateSize($request);

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate   = Carbon::parse($endDate)->endOfDay();
        }

        $profitReports = $this->model
            ->select("id", "current_status_id", "order_from_id", "invoice_number", "buy_price", "net_order_price", "created_at")
            ->selectRaw("net_order_price - buy_price as profit")
            ->with(["currentStatus:id,name", "orderFrom:id,name"])
            ->withSum("details as order_quantity", "quantity")
            ->where("paid_status", StatusEnum::PAID)
            ->when($orderFromId, fn($query) => $query->where("order_from_id", $orderFromId))
            ->when($startDate && $endDate, fn($query) => $query->whereBetween("created_at", [$startDate, $endDate]))
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

        return [
            "total_order"           => $profitReports->count(),
            "total_quantity"        => $profitReports->sum("order_quantity"),
            "total_buy_price"       => $profitReports->sum("buy_price"),
            "total_net_order_price" => $profitReports->sum("net_order_price"),
            "total_profit"          => $profitReports->sum("profit"),
            "profit_report"         => $profitReports,
        ];

        return $orderProfitReport;
    }
    
    public function orderCancelReport($request)
    {
        $lastCancelOrders = $this->model->select("id", "phone_number","customer_name","payable_price", "created_at")->where('current_status_id', 8)->latest()->take(10)->get();

        return $lastCancelOrders;
    }
    
    public function orderReturnReport($request)
    {
        $returnOrders = $this->model
        ->select("id", "phone_number","customer_name","payable_price", "created_at")
        ->where('current_status_id', 10)
        ->latest()
        ->get();

        return $returnOrders;
    }

    public function purchaseReport($request)
    {
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);
        $supplierId   = $request->input("supplier_id", null);
        $paginateSize = Helper::checkPaginateSize($request);

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate   = Carbon::parse($endDate)->endOfDay();
        }

        $purchaseReports = Purchase::select(
            "id",
            "supplier_id",
            "purchase_code",
            "cost",
            "quantity",
            "buy_price",
            "due_amount",
            "paid_amount",
            "paid_status",
            "purchase_status",
            "purchase_date"
        )
            ->with(["supplier:id,name"])
            ->when($supplierId, fn($query) => $query->where("supplier_id", $supplierId))
            ->when($startDate && $endDate, fn($query) => $query->whereBetween("purchase_date", [$startDate, $endDate]))
            ->orderBy("purchase_date", "desc")
            ->paginate($paginateSize);

        return [
            "total_purchase"    => $purchaseReports->count(),
            "total_quantity"    => $purchaseReports->sum("quantity"),
            "total_cost"        => $purchaseReports->sum("cost"),
            "total_buy_price"   => $purchaseReports->sum("buy_price"),
            "total_due_amount"  => $purchaseReports->sum("due_amount"),
            "total_paid_amount" => $purchaseReports->sum("paid_amount"),
            "purchase_report"   => $purchaseReports
        ];
    }

    public function supplierReport($request)
    {
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);
        $paginateSize = Helper::checkPaginateSize($request);

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate   = Carbon::parse($endDate)->endOfDay();
        }

        $supplierReports = Supplier::select("id", "name", "phone_number", "created_at")
            ->withCount("purchases")
            ->withSum("purchases as buy_price", "buy_price")
            ->withSum("purchases as due_amount", "due_amount")
            ->withSum("purchases as paid_amount", "paid_amount")
            ->when($startDate && $endDate, fn($query) => $query->whereBetween("created_at", [$startDate, $endDate]))
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

        return [
            "total_supplier"    => $supplierReports->count(),
            "total_purchase"    => $supplierReports->sum("purchases_count"),
            "total_buy_price"   => $supplierReports->sum("buy_price"),
            "total_due_amount"  => $supplierReports->sum("due_amount"),
            "total_paid_amount" => $supplierReports->sum("paid_amount"),
            "supplier_report"   => $supplierReports
        ];
    }

    public function expenseReport($request)
    {
        $categoryId   = $request->input("category_id", null);
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);
        $paginateSize = Helper::checkPaginateSize($request);

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate   = Carbon::parse($endDate)->endOfDay();
        }

        $expenseReports = Expense::select("id", "category_id", "amount", "expense_date", "created_at")
            ->with(["category:id,name"])
            ->when($categoryId, fn($query) => $query->where("category_id", $categoryId))
            ->when($startDate && $endDate, fn($query) => $query->whereBetween("expense_date", [$startDate, $endDate]))
            ->orderBy("expense_date", "desc")
            ->paginate($paginateSize);

        return [
            "total_expense"  => $expenseReports->count(),
            "total_amount"   => $expenseReports->sum("amount"),
            "expense_report" => $expenseReports
        ];
    }

    public function netProfitReport($request)
    {
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);
        $paginateSize = Helper::checkPaginateSize($request);

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfMonth();
            $endDate   = Carbon::parse($endDate)->endOfMonth();
        }

        // Fetch Expense Reports
        $expenseData = Expense::select(
            DB::raw("DATE_FORMAT(expense_date, '%Y-%m') as month"),
            DB::raw("DATE_FORMAT(expense_date, '%M %Y') as month_name"),
            DB::raw("SUM(amount) as monthly_expense")
        )
            ->when($startDate && $endDate, fn($query) => $query->whereBetween("expense_date", [$startDate, $endDate]))
            ->groupBy(
                DB::raw("DATE_FORMAT(expense_date, '%Y-%m')"),
                DB::raw("DATE_FORMAT(expense_date, '%M %Y')")
            )
            ->orderBy(DB::raw("DATE_FORMAT(expense_date, '%Y-%m')"), "desc")
            ->get();

        // Fetch Sell Reports
        $sellData = $this->model->select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw("DATE_FORMAT(created_at, '%M %Y') as month_name"),
            DB::raw("SUM(net_order_price - buy_price) as monthly_sell")
        )
            ->when($startDate && $endDate, fn($query) => $query->whereBetween("created_at", [$startDate, $endDate]))
            ->groupBy(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m')"),
                DB::raw("DATE_FORMAT(created_at, '%M %Y')")
            )
            ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), "desc")
            ->get();

        // Merge all months from both sell and expense reports
        $allMonths = collect($expenseData)
            ->pluck("month")
            ->merge($sellData->pluck("month"))
            ->unique()
            ->sortDesc();

        // Generate the final profit report
        $mergedData = $allMonths->map(function ($month) use ($sellData, $expenseData) {
            $sell    = collect($sellData)->firstWhere("month", $month);
            $expense = collect($expenseData)->firstWhere("month", $month);

            return [
                "month"           => $month,
                "month_name"      => $sell->month_name ?? $expense->month_name,
                "monthly_sell"    => $sell->monthly_sell ?? 0,
                "monthly_expense" => $expense->monthly_expense ?? 0,
                "monthly_profit"  => ($sell->monthly_sell ?? 0) - ($expense->monthly_expense ?? 0)
            ];
        });

        // Paginate the final merged data manually
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedData   = new LengthAwarePaginator(
            $mergedData->forPage($currentPage, $paginateSize),
            $mergedData->count(),
            $paginateSize,
            $currentPage,
            ["path" => request()->url()]
        );

        return [
            "data"             => $pagedData->values(),
            "pagination"       => [
                "current_page" => $pagedData->currentPage(),
                "per_page"     => $pagedData->perPage(),
                "total"        => $pagedData->total(),
                "last_page"    => $pagedData->lastPage(),
            ]
        ];
    }

    public function incompleteOrderReport($request)
    {
        $limit = (int) ($request->limit ?? 10);

        $products = Product::select([
                'id',
                'name',
                'incomplete_order_count',
                'buy_price',
                'mrp',
                'offer_price',
                'discount',
                'sell_price',
                'current_stock',
                'img_path',
            ])
            ->where('incomplete_order_count', '>', 0)
            ->orderByDesc('incomplete_order_count')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'id'                     => $item->id,
                'name'                   => $item->name,
                'incomplete_order_count' => $item->incomplete_order_count,
                'buy_price'              => $item->buy_price,
                'mrp'                    => $item->mrp,
                'offer_price'            => $item->offer_price,
                'discount'               => $item->discount,
                'sell_price'             => $item->sell_price,
                'current_stock'          => $item->current_stock,
                'img_path'               => Helper::getFilePath($item->img_path),
            ]);

        $incompleteCounts = IncompleteOrder::selectRaw("
            SUM(CASE
                WHEN DATE(created_at) = CURDATE()
                THEN 1 ELSE 0
            END) as today,

            SUM(CASE
                WHEN DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                THEN 1 ELSE 0
            END) as yesterday,

            SUM(CASE
                WHEN YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
                THEN 1 ELSE 0
            END) as this_week,

            SUM(CASE
                WHEN MONTH(created_at) = MONTH(CURDATE())
                AND YEAR(created_at) = YEAR(CURDATE())
                THEN 1 ELSE 0
            END) as this_month
        ")
        ->first();

        $convertedCounts = $this->model
            ->where('is_incomplete', 1)
            ->selectRaw("
                SUM(CASE
                    WHEN DATE(created_at) = CURDATE()
                    THEN 1 ELSE 0
                END) as today,

                SUM(CASE
                    WHEN DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                    THEN 1 ELSE 0
                END) as yesterday,

                SUM(CASE
                    WHEN YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
                    THEN 1 ELSE 0
                END) as this_week,

                SUM(CASE
                    WHEN MONTH(created_at) = MONTH(CURDATE())
                    AND YEAR(created_at) = YEAR(CURDATE())
                    THEN 1 ELSE 0
                END) as this_month
            ")
            ->first();

        return [
            'products'                     => $products,

            'today_orders'                 => (int) $incompleteCounts->today,
            'yesterday_orders'             => (int) $incompleteCounts->yesterday,
            'this_week_orders'             => (int) $incompleteCounts->this_week,
            'this_month_orders'            => (int) $incompleteCounts->this_month,

            'today_converted_orders'       => (int) $convertedCounts->today,
            'yesterday_converted_orders'   => (int) $convertedCounts->yesterday,
            'this_week_converted_orders'   => (int) $convertedCounts->this_week,
            'this_month_converted_orders'  => (int) $convertedCounts->this_month,
        ];
    }

    public function getDownSellReport($request)
    {
        $topProducts = OrderDetail::where('is_upsell', 1)
            ->select(
                'product_id',
                DB::raw('SUM(quantity * sell_price) as total_amount'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->with('product:id,name,img_path')
            ->get();

        $totalRevenue = OrderDetail::where('is_upsell', 1)
            ->select(DB::raw('SUM(quantity * sell_price) as total'))
            ->value('total') ?? 0;

        $totalQuantity = OrderDetail::where('is_upsell', 1)
            ->select(DB::raw('SUM(quantity) as qty'))
            ->value('qty') ?? 0;

        $topProducts->transform(function ($item) use ($totalRevenue) {

            $item->percentage = $totalRevenue > 0 ? round(($item->total_amount / $totalRevenue) * 100, 2) : 0;

            $item->image = $item->product?->img_path ? Helper::getFilePath($item->product->img_path) : null;

            return $item;
        });

        return [
            "stats" => [
                "total_sell"     => $totalQuantity,
                "total_revenue"  => $totalRevenue,
                "sell_rate"      => $totalRevenue > 0 ? round(($totalQuantity / $totalRevenue) * 100, 2) : 0,
            ],
            "top_products" => $topProducts
        ];
    }
    
    public function getLowestProducts($request)
    {
        $lowestStockProducts = Product::select('id', 'name', 'current_stock', 'img_path')
        ->where('status', 'active')
        ->whereNotNull('current_stock')
        ->orderBy('current_stock', 'asc')
        ->take(10)
        ->get()->map(function ($product) {
            $product->img_path = Helper::getFilePath($product->img_path);
            return $product;
        });


        return $lowestStockProducts;
    }
    
    public function downsellReport($request)
    {
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);
        $orderFromId  = $request->input("order_from_id", null);
        $paginateSize = Helper::checkPaginateSize($request);

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate   = Carbon::parse($endDate)->endOfDay();
        }

        $orders = $this->model
            ->select("id", "current_status_id", "order_from_id", "invoice_number", "payable_price", "paid_status", "phone_number", "created_at")
            ->with(["currentStatus:id,name", "orderFrom:id,name"])
            ->withSum("details as order_quantity", "quantity")
            ->where("is_down_sell", 1)
            ->when($orderFromId, fn($query) => $query->where("order_from_id", $orderFromId))
            ->when($startDate && $endDate, fn($query) => $query->whereBetween("created_at", [$startDate, $endDate]))
            ->orderBy("created_at", "DESC")
            ->paginate($paginateSize);

        return [
            "total_order"    => $orders->count(),
            "total_amount"   => $orders->sum("payable_price"),
            "total_quantity" => $orders->sum("order_quantity"),
            "orders"         => $orders,
        ];
    }
    
    public function followUpReport($request)
    {
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);
        $orderFromId  = $request->input("order_from_id", null);
        $paginateSize = Helper::checkPaginateSize($request);

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate   = Carbon::parse($endDate)->endOfDay();
        }

        $orders = $this->model
            ->select("id", "current_status_id", "order_from_id", "invoice_number", "payable_price", "paid_status", "phone_number", "created_at")
            ->with(["currentStatus:id,name", "orderFrom:id,name"])
            ->withSum("details as order_quantity", "quantity")
            ->where("is_follow_order", 1)
            ->when($orderFromId, fn($query) => $query->where("order_from_id", $orderFromId))
            ->when($startDate && $endDate, fn($query) => $query->whereBetween("created_at", [$startDate, $endDate]))
            ->orderBy("created_at", "DESC")
            ->paginate($paginateSize);

        return [
            "total_order"    => $orders->count(),
            "total_amount"   => $orders->sum("payable_price"),
            "total_quantity" => $orders->sum("order_quantity"),
            "orders"         => $orders,
        ];
    }
}
