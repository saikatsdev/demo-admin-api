<?php

namespace App\Repositories\Order;


use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\Order;
use Jenssegers\Agent\Agent;
use App\Models\Order\Status;
use App\Enums\OrderStatusEnum;
use App\Events\NewOrderPlaced;
use App\Models\Order\DownSell;
use App\Models\Order\FollowUp;
use App\Enums\DiscountTypeEnum;
use App\Models\Order\BlockUser;
use App\Models\Product\Product;
use App\Models\Order\OrderGuard;
use App\Models\Product\Campaign;
use App\Models\Order\OrderDetail;
use App\Models\Order\CustomerType;
use App\Models\Order\FreeDelivery;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Models\Product\UpSellDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\Order\DeliveryGateway;
use App\Models\Order\IncompleteOrder;
use App\Models\Product\ProductVariation;
use App\Models\Order\IncompleteOrderDetail;
use App\Models\Order\OnlinePaymentDiscount;

class OrderRepository
{
    protected $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);

        if ($startDate && $endDate) {
            $startDate = Helper::startOfDate($startDate);
            $endDate   = Helper::endOfDate($endDate);
        }

        $orders = $this->model->with([
            "currentStatus:id,name,bg_color,text_color",
            "paymentGateway:id,name",
            "preparedBy:id,username",
            "createdBy:id,username",
            "updatedBy:id,username",
            "orderFrom:id,name",
            "notes",
            "notes.createdBy:id,username",
            "notes.updatedBy:id,username",
            "courier:id,name",
            "assignUser:id,username",
            "customerType:id,name",
            "cancelReason:id,name"
        ])
            ->when((Helper::isModuleActive("TeamManagement") && !Helper::isAdminOrTeamLead()), fn($query) => $query->where("assign_user_id", Auth::id()))
            ->when($request->current_status_id, fn($query) => $query->where("current_status_id", $request->current_status_id))
            ->when($request->paid_status, fn($query) => $query->where("paid_status", $request->paid_status))
            ->when($request->phone_number, fn($query) => $query->where("phone_number", $request->phone_number))
            ->when($request->order_from_id, fn($query) => $query->where("order_from_id", $request->order_from_id))
            ->when($request->courier_id, fn($query) => $query->where("courier_id", $request->courier_id))
            ->when($request->coupon_id, fn($query) => $query->where("coupon_id", $request->coupon_id))
            ->when($request->prepared_by, fn($query) => $query->where("prepared_by", $request->prepared_by))
            ->when($request->assign_user_id, fn($query) => $query->where("assign_user_id", $request->assign_user_id))
            ->when($request->district_id, fn($query) => $query->where("district_id", $request->district_id))
            ->when($request->payment_gateway_id, fn($query) => $query->where("payment_gateway_id", $request->payment_gateway_id))
            ->when($request->delivery_gateway_id, fn($query) => $query->where("delivery_gateway_id", $request->delivery_gateway_id))
            ->when($request->cancel_reason_id, fn($query) => $query->where("cancel_reason_id", $request->cancel_reason_id))
            ->when($request->customer_type_id, fn($query) => $query->where("customer_type_id", $request->customer_type_id))
            ->when($request->is_invoice_printed, fn($query) => $query->where("is_invoice_printed", $request->is_invoice_printed))
            ->when($request->is_duplicate, fn($query) => $query->where("is_duplicate", $request->is_duplicate)->where("current_status_id", OrderStatusEnum::PENDING))
            ->when(($startDate && $endDate), fn($query) => $query->whereBetween("created_at", [$startDate, $endDate]))
            ->when(($request->min_price && $request->max_price), fn($query) => $query->whereBetween("net_order_price", [$request->min_price, $request->max_price]))
            ->when(($request->min_invoice && $request->max_invoice), fn($query) => $query->whereBetween("invoice_number", [$request->min_invoice, $request->max_invoice]))
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("phone_number", "like", "%$searchKey%")
                    ->orWhere("customer_name", "like", "%$searchKey%")
                    ->orWhere("invoice_number", "like", "%$searchKey%");
            })
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

        return $orders;
    }

    public function latestOrders()
    {
        $orders = $this->model->with([
            "details:id,order_id,product_id",
            "details.product:id,name,slug,img_path",
            "currentStatus:id,name,bg_color,text_color",
            "customerType:id,name",
            "assignUser:id,username",
        ])
        ->orderBy("created_at", "desc")
        ->limit(5)
        ->get(['id','customer_name','phone_number','net_order_price','created_at','current_status_id', 'customer_type_id','assign_user_id']);

        return $orders;
    }

    public function list($request)
    {
        $paginateSize = $request->input("paginate_size", config('app.paginate_size'));
        $searchKey    = $request->input("search_key", null);
        $startDate    = $request->input("start_date", null);
        $endDate      = $request->input("end_date", null);

        if ($startDate && $endDate) {
            $startDate = Helper::startOfDate($startDate);
            $endDate   = Helper::endOfDate($endDate);
        }

        $baseQuery = $this->model
        ->when($request->current_status_id, fn($query) => $query->where("current_status_id", $request->current_status_id))
        ->when($request->paid_status, fn($q) => $q->where("paid_status", $request->paid_status))
        ->when($request->order_from_id, fn($q) => $q->where("order_from_id", $request->order_from_id))
        ->when($request->courier_id, fn($q) => $q->where("courier_id", $request->courier_id))
        ->when($request->district_id, fn($q) => $q->where("district_id", $request->district_id))
        ->when($request->customer_type_id, fn($q) => $q->where("customer_type_id", $request->customer_type_id))
        ->when(($startDate && $endDate), fn($q) => $q->whereBetween("created_at", [$startDate, $endDate]))
        ->when($searchKey, function ($q) use ($searchKey) {
            $q->where("phone_number", "like", "%$searchKey%")
                ->orWhere("invoice_number", "like", "%$searchKey%");
        });

        $overallTotals = (clone $baseQuery)
        ->selectRaw('
            COUNT(id) AS total_orders,
            COALESCE(SUM(payable_price), 0) AS total_order_price
        ')->first();

        $statusReport = Status::active()
        ->leftJoin('orders', function ($join) use ($request, $startDate, $endDate, $searchKey) {
            $join->on('orders.current_status_id', '=', 'statuses.id')->whereNull('orders.deleted_at');

            // if ($request->current_status_id) {
            //     $join->where('orders.current_status_id', $request->current_status_id);
            // }

            if ($request->paid_status) {
                $join->where('orders.paid_status', $request->paid_status);
            }

            if ($request->order_from_id) {
                $join->where('orders.order_from_id', $request->order_from_id);
            }

            if ($request->courier_id) {
                $join->where('orders.courier_id', $request->courier_id);
            }

            if ($request->district_id) {
                $join->where('orders.district_id', $request->district_id);
            }

            if ($request->customer_type_id) {
                $join->where('orders.customer_type_id', $request->customer_type_id);
            }

            if ($startDate && $endDate) {
                $join->whereBetween('orders.created_at', [$startDate, $endDate]);
            }

            if ($searchKey) {
                $join->where(function ($q) use ($searchKey) {
                    $q->where('orders.phone_number', 'like', "%$searchKey%")
                        ->orWhere('orders.invoice_number', 'like', "%$searchKey%");
                });
            }
        })
        ->selectRaw("
            statuses.id   AS status_id,
            statuses.name AS status_name,

            COUNT(orders.id) AS order_count,

            COALESCE(SUM(orders.payable_price), 0) AS total_payable,

            COUNT(
                CASE
                    WHEN orders.current_status_id = ?
                    AND orders.courier_status_id = ?
                    THEN orders.id
                END
            ) AS courier_pending_count,

            COUNT(
                CASE
                    WHEN orders.current_status_id = ?
                    AND orders.courier_status_id = ?
                    THEN orders.id
                END
            ) AS courier_received_count,

            COALESCE(
                SUM(
                    CASE
                        WHEN orders.current_status_id = ?
                        AND orders.courier_status_id = ?
                        THEN orders.payable_price
                    END
                ), 0
            ) AS courier_pending_amount,

            COALESCE(
                SUM(
                    CASE
                        WHEN orders.current_status_id = ?
                        AND orders.courier_status_id = ?
                        THEN orders.payable_price
                    END
                ), 0
            ) AS courier_received_amount
        ", [
            OrderStatusEnum::ON_THE_WAY,
            OrderStatusEnum::COURIER_PENDING,
            OrderStatusEnum::ON_THE_WAY,
            OrderStatusEnum::COURIER_RECEIVED,
            OrderStatusEnum::ON_THE_WAY,
            OrderStatusEnum::COURIER_PENDING,
            OrderStatusEnum::ON_THE_WAY,
            OrderStatusEnum::COURIER_RECEIVED,
        ])
        ->groupBy('statuses.id', 'statuses.name')
        ->orderBy('statuses.id')
        ->get();

        $orders = (clone $baseQuery)
        ->select('id', 'invoice_number', 'coupon_value', 'delivery_charge', 'buy_price', 'mrp', 'sell_price',
            'discount', 'special_discount', 'net_order_price', 'advance_payment', 'payable_price', 'courier_payable',
            'paid_status', 'payment_gateway_id', 'current_status_id', 'order_from_id', 'courier_id', 'phone_number',
            'customer_name', 'address_details', 'district_id', 'customer_type_id', 'is_invoice_printed', 'is_duplicate',
            'created_at', 'consignment_id', 'tracking_code', 'callback_response'
        )
        ->with([
            "details:id,order_id,product_id",
            "details.product:id,name,slug,img_path",
            "currentStatus:id,name,bg_color,text_color",
            "paymentGateway:id,name",
            "orderFrom:id,name",
            "courier:id,name",
            "courierStatus:id,name",
            "customerType:id,name",
        ])
        ->latest()
        ->paginate($paginateSize);

        return [
            'orders'            => $orders,
            'total_orders'      => (int) $overallTotals->total_orders,
            'total_order_price' => (float) $overallTotals->total_order_price,
            'status_report'     => $statusReport
        ];
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $order = new $this->model();

            $order->payment_gateway_id  = $request->payment_gateway_id;
            $order->delivery_gateway_id = $request->delivery_gateway_id;
            $order->current_status_id   = $request->current_status_id;
            $order->coupon_id           = $request->coupon_id;
            $order->order_from_id       = $request->order_from_id ?? 2;
            $order->assign_user_id      = $request->assign_user_id;
            $order->courier_id          = $request->courier_id;
            $order->district_id         = $request->district_id;
            $order->customer_type_id    = $request->customer_type_id;

            $order->invoice_number = Helper::generateInvoiceNumber(Order::class,'invoice_number',4);

            $order->paid_status         = $request->paid_status;
            $order->delivery_charge     = $request->delivery_charge ?? 0;
            $order->special_discount    = $request->special_discount ?? 0;
            $order->advance_payment     = $request->advance_payment ?? 0;
            $order->address_details     = $request->address_details;
            $order->pickup_store_id     = $request->pickup_store_id;
            $order->courier_area_id     = $request->courier_area_id ?? null;
            $order->is_duplicate        = $this->isDuplicateOrder($request);
            $order->is_follow_order     = $request->is_follow_order;
            $order->phone_number        = $request->phone_number;
            $order->customer_name       = $request->customer_name;
            $order->note                = $request->order_note;
            $order->delivery_type       = $request->delivery_type ?? 48;
            $order->item_weight         = $request->item_weight;
            $order->delivery_area       = $request->delivery_area;
            $order->is_incomplete       = $request->is_incomplete ?? 0;
            $order->save();

            $itemDetails = [];
            foreach ($request->items as $item) {
                $productId         = $item["product_id"];
                $attributeValueId1 = $item["attribute_value_id_1"];
                $attributeValueId2 = $item["attribute_value_id_2"];
                $attributeValueId3 = $item["attribute_value_id_3"];
                $quantity          = $item["quantity"];

                $product = Product::select("id", "name", "buy_price", "mrp", "offer_price", "discount", "sell_price", "current_stock","total_sell_qty", "free_shipping")
                    ->with("variations")
                    ->where("status", StatusEnum::ACTIVE)
                    ->where("id", $productId)
                    ->first();

                if (!$product) {
                    throw new CustomException("Product not fount");
                }

                if ($product->variations && count($product->variations) > 0) {
                    $productVariation = ProductVariation::where("product_id", $productId)
                        ->where("attribute_value_id_1", $attributeValueId1)
                        ->where("attribute_value_id_2", $attributeValueId2)
                        ->where("attribute_value_id_3", $attributeValueId3)
                        ->first();

                    if (!$productVariation) {
                        throw new CustomException("Variation $product->name not found");
                    }

                    $productVariation->total_sell_qty += $quantity;

                    if (Helper::setting("is_stock_maintain") && $request->current_status_id == OrderStatusEnum::APPROVED) {
                        $attributeValue1 = @$productVariation->attributeValue1->value ?? null;
                        $attributeValue2 = @$productVariation->attributeValue2->value ?? null;
                        $attributeValue3 = @$productVariation->attributeValue3->value ?? null;

                        if (!Helper::setting("is_negative_stock_allow")) {
                            if ($productVariation->current_stock < $quantity) {
                                throw new CustomException("$product->name $attributeValue1 $attributeValue2 $attributeValue3 is out of stock");
                            }
                        }

                        $productVariation->current_stock  -= $quantity;


                        if ($productVariation->current_stock <= $product->alert_qty) {
                            info("$product->name $attributeValue1 $attributeValue2 $attributeValue3 quantity is $productVariation->current_stock");
                        }
                    }

                    $productVariation->save();

                    $buyPrice  = $productVariation->buy_price;
                    $mrp       = $productVariation->mrp;
                    $discount  = $productVariation->discount;
                    $sellPrice = $productVariation->sell_price;
                } else {
                    $product->total_sell_qty += $quantity;

                    if (Helper::setting("is_stock_maintain") && $request->current_status_id == OrderStatusEnum::APPROVED) {
                        if (!Helper::setting("is_negative_stock_allow")) {
                            if ($product->current_stock < $quantity) {
                                throw new CustomException("$product->name is out of stock");
                            }
                        }

                        $product->current_stock  -= $quantity;

                        if ($product->current_stock <= $product->alert_qty) {
                            info("The $product->name quantity is $product->current_stock");
                        }
                    }

                    $product->save();

                    $buyPrice  = $product->buy_price;
                    $mrp       = $product->mrp;
                    $discount  = $product->discount;
                    $sellPrice = $product->sell_price;
                }

                $itemDetails[] = [
                    'order_id'             => $order->id,
                    'product_id'           => $product->id,
                    'attribute_value_id_1' => $attributeValueId1,
                    'attribute_value_id_2' => $attributeValueId2,
                    'attribute_value_id_3' => $attributeValueId3,
                    'quantity'             => $quantity,
                    'buy_price'            => $buyPrice,
                    'mrp'                  => $mrp,
                    'sell_price'           => $sellPrice,
                    'discount'             => $discount,
                    'created_at'           => now()
                ];
            }

            // insert order details
            OrderDetail::insert($itemDetails);

            // attach order status
            $order->statuses()->attach($request->current_status_id);

            // Update buy_price, mrp, sell_price, discount, net_value, payable and coupon_value
            $order->updateOrderValue($order);

            // Check current status on the way and is active courier module
            if ($request->current_status_id == OrderStatusEnum::ON_THE_WAY) {
                // Steadfast
                if ($order->courier_id == 1) {
                    $request->merge(["order_id" => $order->id]);
                    (new SteadFastRepository)->createOrder($request);
                }

                // Pathao
                if ($order->courier_id == 2 && $order->pickup_store_id && $order->courier_area_id) {
                    $request->merge(["order_id" => $order->id]);
                    (new PathaoRepository)->createOrder($request);
                }

                // Redex
                if ($order->courier_id == 3 && $order->pickup_store_id && $order->courier_area_id) {
                    $request->merge(["order_id" => $order->id]);
                    (new RedxRepository)->parcelCreate($request);
                }
            }

            if ($request->incomplete_order_id) {
                $incompleteOrder = IncompleteOrder::find($request->incomplete_order_id);

                if ($incompleteOrder) {
                    $incompleteOrder->update(['status_id' => OrderStatusEnum::APPROVED]);
                }
            }

            return $order;
        });
    }

    public function show($id)
    {
        $order = $this->model->with(
            [
                "createdBy:id,username",
                "updatedBy:id,username",
                "preparedBy:id,username",
                "deliveryGateway:id,name",
                "paymentGateway:id,name",
                "coupon:id,name,code",
                "currentStatus:id,name,bg_color,text_color",
                "statuses:id,name,bg_color,text_color",
                "orderFrom:id,name",
                "details",
                "details.attributeValue1:id,value,attribute_id",
                "details.attributeValue2:id,value,attribute_id",
                "details.attributeValue3:id,value,attribute_id",
                "details.attributeValue1.attribute:id,name",
                "details.attributeValue2.attribute:id,name",
                "details.attributeValue3.attribute:id,name",
                "details.product:id,name,img_path,sku,current_stock,img_path",
                "notes",
                "notes.createdBy:id,username",
                "notes.updatedBy:id,username",
                "transaction",
                "courier:id,name,slug",
                "district:id,name,slug",
                "assignUser:id,username",
                "customerType:id,name",
                "cancelReason:id,name",
            ]
        )->find($id);

        if (!$order) {
            throw new CustomException("Order not found");
        }

        return $order;
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $order = $this->model->with("details")->find($id);

            if (!$order) {
                throw new CustomException("Order not found");
            }

            // Check order status is cancel and request status also cancel
            if ($request->current_status_id == OrderStatusEnum::CANCELED && $request->current_status_id == $order->current_status_id) {
                throw new CustomException("The order already cancel status");
            }

            // Check order is approved
            $checkOrderIsApproved = $order->statuses->contains('id', OrderStatusEnum::APPROVED);

            // Add previous quantity with product current stock
            if (Helper::setting("is_stock_maintain") && $checkOrderIsApproved) {
                foreach ($order->details as $item) {
                    $previousProduct = Product::with(["variations"])->find($item->product_id);

                    if ($previousProduct) {
                        if (!empty($previousProduct->variations)) {
                            $previousVariation = ProductVariation::where("product_id", $item->product_id)
                                ->where("attribute_value_id_1", $item->attribute_value_id_1)
                                ->where("attribute_value_id_2", $item->attribute_value_id_2)
                                ->where("attribute_value_id_3", $item->attribute_value_id_3)
                                ->first();

                            if ($previousVariation) {
                                $previousVariation->total_sell_qty -= $item->quantity;
                                $previousVariation->current_stock  += $item->quantity;
                                $previousVariation->save();
                            }
                        } else {
                            $previousProduct->total_sell_qty -= $item->quantity;
                            $previousProduct->current_stock  += $item->quantity;
                            $previousProduct->save();
                        }
                    }
                }
            }

            $order = $this->model->find($id);

            $order->payment_gateway_id  = $request->payment_gateway_id;
            $order->delivery_gateway_id = $request->delivery_gateway_id;
            $order->current_status_id   = $request->current_status_id;
            $order->coupon_id           = $request->coupon_id;
            $order->order_from_id       = $request->order_from_id;
            $order->courier_id          = $request->courier_id;
            $order->assign_user_id      = $request->assign_user_id;
            $order->district_id         = $request->district_id;
            $order->pickup_store_id     = $request->pickup_store_id;
            $order->customer_type_id    = $request->customer_type_id;
            $order->cancel_reason_id    = $request->cancel_reason_id;
            $order->paid_status         = $request->paid_status;
            $order->delivery_charge     = $request->delivery_charge ?? 0;
            $order->special_discount    = $request->special_discount ?? 0;
            $order->advance_payment     = $request->advance_payment ?? 0;
            $order->address_details     = $request->address_details;
            $order->courier_area_id     = $request->courier_area_id ?? null;
            $order->customer_name       = $request->customer_name;
            $order->phone_number        = $request->phone_number;
            $order->note                = $request->order_note;
            $order->delivery_type       = $request->delivery_type ?? 48;
            $order->item_weight         = $request->item_weight;
            $order->delivery_area       = $request->delivery_area;
            $order->save();

            $itemDetails = [];

            if ($request->current_status_id == 3) {

                if ($request->filled('approx_start_date') || $request->filled('approx_end_date')) {

                    FollowUp::updateOrCreate(
                        [
                            'order_id' => $order->id,
                        ],
                        [
                            'start_date' => $request->approx_start_date,
                            'end_date'   => $request->approx_end_date,
                            'note'       => $request->follow_note,
                        ]
                    );
                }
            }

            // Check order current status is not cancel
            if ($request->current_status_id != OrderStatusEnum::CANCELED) {
                foreach ($request->items as $item) {
                    $productId         = $item["product_id"];
                    $attributeValueId1 = @$item["attribute_value_id_1"];
                    $attributeValueId2 = @$item["attribute_value_id_2"];
                    $attributeValueId3 = @$item["attribute_value_id_3"];
                    $quantity          = $item["quantity"];
                    $buyPrice          = $item["buy_price"];
                    $mrp               = $item["mrp"];
                    $sellPrice         = $item["sell_price"];
                    $discount          = $item["discount"];

                    $product = Product::select("id", "name", "buy_price", "mrp", "offer_price", "discount", "sell_price", "current_stock", "free_shipping")
                        ->with("variations")
                        ->find($productId);

                    // Stock calculation
                    if ($product && Helper::setting("is_stock_maintain") && ($checkOrderIsApproved || $request->current_status_id == OrderStatusEnum::APPROVED)) {
                        if (!empty($product->variations)) {
                            $variation = ProductVariation::where("product_id", $productId)
                                ->where("attribute_value_id_1", $attributeValueId1)
                                ->where("attribute_value_id_2", $attributeValueId2)
                                ->where("attribute_value_id_3", $attributeValueId3)
                                ->first();

                            if ($variation) {
                                $attributeValue1 = @$variation->attributeValue1->value ?? null;
                                $attributeValue2 = @$variation->attributeValue2->value ?? null;
                                $attributeValue3 = @$variation->attributeValue3->value ?? null;

                                // Check negative sell allow
                                if (!Helper::setting("is_negative_stock_allow")) {
                                    if ($variation->current_stock < $quantity) {
                                        throw new CustomException("$product->name $attributeValue1 $attributeValue2 $attributeValue3 out of stock");
                                    }
                                }

                                // Update variation product stock
                                $variation->total_sell_qty += $quantity;
                                $variation->current_stock  -= $quantity;
                                $variation->save();

                                // Check alert quantity
                                if ($variation->current_stock <= $product->alert_qty) {
                                    info("The $product->name $attributeValue1 $attributeValue2, $attributeValue3 quantity is $variation->current_stock");
                                }
                            }
                        } else {
                            if (!Helper::setting("is_negative_stock_allow")) {
                                if ($product && $product->current_stock < $quantity) {
                                    throw new CustomException("$product->name out of stock");
                                }
                            }

                            // Update product current stock
                            $product->total_sell_qty += $quantity;
                            $product->current_stock  -= $quantity;
                            $product->save();

                            // Check alert quantity
                            if ($product->current_stock <= $product->alert_qty) {
                                info("The $product->name quantity is $product->current_stock");
                            }
                        }
                    }

                    $isUpsell = (!isset($item['is_upsell']) || $item['is_upsell'] === 'undefined') ? false : (bool)$item['is_upsell'];

                    // Prepare order details payload
                    $itemDetails[] = [
                        'order_id'             => $order->id,
                        'product_id'           => $product->id,
                        'attribute_value_id_1' => $attributeValueId1,
                        'attribute_value_id_2' => $attributeValueId2,
                        'attribute_value_id_3' => $attributeValueId3,
                        'quantity'             => $quantity,
                        'buy_price'            => $buyPrice,
                        'mrp'                  => $mrp,
                        'sell_price'           => $sellPrice,
                        'discount'             => $discount,
                        'is_upsell'            => $isUpsell ? 1 : 0,
                        'created_at'           => now()
                    ];
                }

                // Delete previous order details
                $order->details()->delete();

                // Insert order details
                OrderDetail::insert($itemDetails);

                // Update buy_price, mrp, sell_price, discount, net_value, payable and coupon_value
                $order->updateOrderValue($order);

                // Check current status on the way and is active courier module
                if ($request->current_status_id == OrderStatusEnum::ON_THE_WAY) {
                    // Steadfast
                    if ($order->courier_id == 1) {
                        $request->merge(["order_id" => $order->id]);
                        (new SteadFastRepository)->createOrder($request);
                    }

                    // Pathao
                    if ($order->courier_id == 2 && $order->pickup_store_id && $order->courier_area_id) {
                        $request->merge(["order_id" => $order->id]);
                        (new PathaoRepository)->createOrder($request);
                    }

                    // Redex
                    if ($order->courier_id == 3 && $order->pickup_store_id && $order->courier_area_id) {
                        $request->merge(["order_id" => $order->id]);
                        (new RedxRepository)->parcelCreate($request);
                    }
                }
            }

            // Attach order status
            $order->statuses()->syncWithoutDetaching([$request->current_status_id]);

            return $order;
        });
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            throw new CustomException("Order not found");
        }

        return $order->delete();
    }

    public function customerOrder($request)
    {
        return DB::transaction(function () use ($request) {
            $now             = Carbon::now();
            $deliveryCharge  = 0;
            $specialDiscount = 0;

            $userBlock = $this->addUseBlock($request);

            $this->checkUserIsBlock($request);

            $specialDiscount = $this->checkBonusPointApplicable($request);

            $downSell = DownSell::where("id", $request->down_sell_id)
                ->where("status", StatusEnum::ACTIVE)
                ->where("started_at", "<=", $now)
                ->where("ended_at", ">=", $now)
                ->first();

            if ($downSell) {
                $specialDiscount += $downSell->amount;
            }

            $deliveryGateway = DeliveryGateway::find($request->delivery_gateway_id);
            if ($deliveryGateway) {
                $deliveryCharge = $deliveryGateway->delivery_fee;
            }

            $orderCountByPhone = $this->model->where("phone_number", $request->phone_number)->count();

            $customerType = CustomerType::where('order_range', '<=', $orderCountByPhone)
            ->orderBy('order_range', 'desc')
            ->first();

            $order = new $this->model();

            $order->payment_gateway_id  = $request->payment_gateway_id;
            $order->delivery_gateway_id = $request->delivery_gateway_id;
            $order->coupon_id           = $request->coupon_id;
            $order->current_status_id   = OrderStatusEnum::PENDING;
            $order->order_from_id       = 1;
            $order->customer_type_id    = $customerType ? $customerType->id : 1;

            $order->invoice_number = Helper::generateInvoiceNumber(Order::class,'invoice_number',4);

            $order->advance_payment     = $request->advance_payment ?? 0;
            $order->special_discount    = $specialDiscount;
            $order->paid_status         = StatusEnum::UNPAID;
            $order->address_details     = $request->address_details;
            $order->customer_name       = $request->customer_name;
            $order->phone_number        = $request->phone_number;
            $order->note                = $request->order_note;
            $order->block_user_id       = $userBlock->id;
            $order->is_duplicate        = $this->isDuplicateOrder($request);
            $order->is_down_sell        = $downSell ? 1 : 0;
            $order->save();

            $orderDetails = [];
            foreach ($request->items as $item) {
                $productId         = $item["product_id"];
                $attributeValueId1 = $item["attribute_value_id_1"] ?? null;
                $attributeValueId2 = $item["attribute_value_id_2"] ?? null;
                $attributeValueId3 = $item["attribute_value_id_3"] ?? null;
                $quantity          = $item["quantity"] ?? 1;
                $campaignSlug      = $item["campaign_slug"] ?? null;

                $product = Product::select("id", "name", "buy_price", "mrp", "offer_price", "discount", "sell_price", "current_stock", "alert_qty", "total_sell_qty","free_shipping")
                    ->with(["variations"])
                    ->where("status", StatusEnum::ACTIVE)
                    ->find($productId);

                if (!$product) {
                    throw new CustomException("Product not found");
                }

                if ($quantity < $product->minimum_qty) {
                    throw new CustomException("$product->name minimum order quantity $product->minimum_qty");
                }

                $product->total_sell_qty += $quantity;
                $product->save();

                $incompleteOrderProduct = IncompleteOrderDetail::where("product_id", $productId)
                    ->where("attribute_value_id_1", $attributeValueId1)
                    ->where("attribute_value_id_2", $attributeValueId2)
                    ->where("attribute_value_id_3", $attributeValueId3)
                    ->whereHas("incompleteOrder", fn($query) => $query->where("phone_number", $request->phone_number))
                    ->first();

                if ($incompleteOrderProduct) {
                    $incompleteOrderProduct->delete();
                }

                if (count($product->variations) > 0) {
                    $variation = ProductVariation::where("product_id", $productId)
                        ->where("attribute_value_id_1", $attributeValueId1)
                        ->where("attribute_value_id_2", $attributeValueId2)
                        ->where("attribute_value_id_3", $attributeValueId3)
                        ->first();

                    if (!$variation) {
                        throw new CustomException("Variation product not found");
                    }

                    $variation->total_sell_qty += $quantity;
                    $variation->save();

                    $buyPrice   = $variation->buy_price;
                    $mrp        = $variation->mrp;
                    $offerPrice = $variation->offer_price;
                    $discount   = $variation->discount;
                    $sellPrice  = $variation->sell_price;
                } else {
                    $buyPrice   = $product->buy_price;
                    $mrp        = $product->mrp;
                    $offerPrice = $product->offer_price;
                    $discount   = $product->discount;
                    $sellPrice  = $product->sell_price;
                }

                // Check campaign
                if ($campaignSlug) {
                    $campaign = Campaign::with([
                        "campaignProducts" => function ($query) use ($productId) {
                            $query->where("product_id", $productId);
                        },
                        "campaignProducts.campaignProductVariations" => function ($query) use ($attributeValueId1, $attributeValueId2, $attributeValueId3) {
                            $query->where("attribute_value_id_1", $attributeValueId1)
                                ->where("attribute_value_id_2", $attributeValueId2)
                                ->where("attribute_value_id_3", $attributeValueId3);
                        }
                    ])
                        ->where("slug", $campaignSlug)
                        ->first();

                    if (!$campaign) {
                        throw new CustomException("Invalid campaign");
                    }

                    // Check campaign product have variation
                    if (count($campaign->campaignProducts) > 0) {
                        $campaignProduct = $campaign->campaignProducts[0];
                        if (count($campaignProduct->campaignProductVariations) > 0) {
                            $campaignVariationPrice = $campaignProduct->campaignProductVariations->first();

                            $buyPrice   = $campaignVariationPrice->buy_price;
                            $mrp        = $campaignVariationPrice->mrp;
                            $offerPrice = $campaignVariationPrice->offer_price;
                            $discount   = $campaignVariationPrice->discount;
                            $sellPrice  = $offerPrice;
                        } else {
                            $buyPrice   = $campaignProduct->buy_price;
                            $mrp        = $campaignProduct->mrp;
                            $offerPrice = $campaignProduct->offer_price;
                            $discount   = $campaignProduct->discount_value;
                            $sellPrice  = $offerPrice;
                        }
                    }
                }

                // prepare payload for order details
                $orderDetails[] = [
                    "order_id"               => $order->id,
                    "product_id"             => $product->id,
                    'attribute_value_id_1'   => $attributeValueId1,
                    'attribute_value_id_2'   => $attributeValueId2,
                    'attribute_value_id_3'   => $attributeValueId3,
                    "quantity"               => $quantity,
                    "buy_price"              => $buyPrice,
                    "mrp"                    => $mrp,
                    "sell_price"             => $sellPrice,
                    "discount"               => $discount,
                    "created_at"             => now()
                ];

                // Check free shipping exist
                if ($product->free_shipping) {
                    $deliveryCharge = 0;
                }
            }

            $order->delivery_charge = $deliveryCharge;
            $order->save();

            // insert order details data
            OrderDetail::insert($orderDetails);

            // attach order status id
            $order->statuses()->attach(1);

            // Check free delivery applicable
            $this->checkIsFreeDeliveryApplicable($order);

            // Check online payment discount
            $this->onlinePaymentDiscount($request->payment_gateway_id, $order);

            // Update buy_price, mrp, sell_price, discount, net_value, payable and coupon_value
            $order->updateOrderValue($order);

            // Add bonus points with customer account
            $this->addBonusPoint($order);

            // Create transaction
            Helper::createTransaction($order->id, null, $request->payment_gateway_trx_id, $request->payment_send_from_number);

            // Delete incomplete order if product empty
            $incompleteOrder = IncompleteOrder::where("phone_number", $request->phone_number)->first();
            if ($incompleteOrder && $incompleteOrder->details->isEmpty()) {
                $incompleteOrder->forceDelete();
            }

            $order = $order->load("details.product");

            try {
                event(new NewOrderPlaced($order));
            } catch (\Exception $exception) {
                info("Pusher error: " . $exception->getMessage());
            }

            return $order;
        });
    }

    public function upSellOrder($request)
    {
        return DB::transaction(function () use ($request) {
            $order = $this->model->find($request->order_id);

            if (!$order) {
                throw new CustomException("Order not found");
            }

            foreach ($request->items as $item) {
                $productId         = $item["product_id"];
                $attributeValueId1 = $item["attribute_value_id_1"] ?? null;
                $attributeValueId2 = $item["attribute_value_id_2"] ?? null;
                $attributeValueId3 = $item["attribute_value_id_3"] ?? null;
                $quantity          = $item["quantity"] ?? 1;

                $upSellDetail = UpSellDetail::where("id", $item["up_sell_detail_id"])
                ->where("up_sell_product_id", $productId)
                ->whereHas("upSell", fn ($query) => $query->where("status", StatusEnum::ACTIVE)->where("started_at", "<=", Carbon::now())->where("ended_at", ">=", Carbon::now()))
                ->first();

                if (!$upSellDetail) {
                    throw new CustomException("This product is not applicable for this offer");
                }

                $product = Product::with([
                    'variations' => function ($query) use ($attributeValueId1, $attributeValueId2, $attributeValueId3) {
                        $query->where('attribute_value_id_1', $attributeValueId1)->where('attribute_value_id_2', $attributeValueId2)->where('attribute_value_id_3', $attributeValueId3);
                    }
                ])->select('id', 'name', 'current_stock', 'alert_qty', 'buy_price', 'mrp', 'discount', 'sell_price', 'free_shipping')
                ->where('status', StatusEnum::ACTIVE)
                ->where('id', $productId)
                ->first();

                // Check product exist
                if (!$product) {
                    throw new CustomException("Product not found");
                }

                // Check product minimum quantity
                if ($quantity < $product->minimum_qty) {
                    throw new CustomException("$product->name minimum order quantity $product->minimum_qty");
                }

                // Check and delete incomplete product
                $incompleteOrderProduct = IncompleteOrderDetail::where("product_id", $productId)
                ->where("attribute_value_id_1", $attributeValueId1)
                ->where("attribute_value_id_2", $attributeValueId2)
                ->where("attribute_value_id_3", $attributeValueId3)
                ->whereHas("incompleteOrder", fn($query) => $query->where("phone_number", $request->phone_number))
                ->first();

                if ($incompleteOrderProduct) {
                    $incompleteOrderProduct->delete();
                }

                // Check product have variation
                if ($product->variations->isNotEmpty()) {
                    $variation = $product->variations->first();

                    if (!$variation) {
                        throw new CustomException("Variation product not found");
                    }

                    $buyPrice   = $variation->buy_price;
                    $mrp        = $variation->mrp;
                    $discount   = $variation->discount + $upSellDetail->calculated_amount;
                    $sellPrice  = $variation->sell_price - $upSellDetail->calculated_amount;
                } else {
                    $buyPrice   = $product->buy_price;
                    $mrp        = $product->mrp;
                    $discount   = $product->discount + $upSellDetail->calculated_amount;
                    $sellPrice  = $product->sell_price - $upSellDetail->calculated_amount;
                }

                OrderDetail::create([
                    "order_id"             => $order->id,
                    "product_id"           => $productId,
                    "is_up_sell"           => 1,
                    'attribute_value_id_1' => $attributeValueId1,
                    'attribute_value_id_2' => $attributeValueId2,
                    'attribute_value_id_3' => $attributeValueId3,
                    "quantity"             => $quantity,
                    "buy_price"            => $buyPrice,
                    "mrp"                  => $mrp,
                    "sell_price"           => $sellPrice,
                    "discount"             => $discount,
                    "created_at"           => now()
                ]);
            }

            $order->paid_status = StatusEnum::UNPAID;
            $order->save();

            // Update buy_price, mrp, sell_price, discount, net_value, payable and coupon_value
            $order->updateOrderValue($order);

            // Add bonus points with customer account
            $this->addBonusPoint($order);

            // Delete incomplete order if product empty
            $incompleteOrder = IncompleteOrder::where("phone_number", $order->phone_number)->first();

            if ($incompleteOrder && $incompleteOrder->details->isEmpty()) {
                $incompleteOrder->forceDelete();
            }

            return $order->load("details.product");
        });
    }

    public function customerOrderList($request)
    {
        $searchKey       = $request->input('search_key', null);
        $currentStatusId = $request->input('current_status_id', null);
        $paidStatus      = $request->input('paid_status', null);
        $paginateSize    = Helper::checkPaginateSize($request);

        $orders = $this->model->with([
            "deliveryGateway:id,name",
            "paymentGateway:id,name",
            "coupon:id,name,code",
            "currentStatus:id,name,bg_color,text_color",
            "statuses:id,name,bg_color,text_color",
            "details",
            "details.attributeValue1:id,value",
            "details.attributeValue2:id,value",
            "details.attributeValue3:id,value",
            "details.product:id,name,img_path",
        ])
            ->when($currentStatusId, fn($query) => $query->where("current_status_id", $currentStatusId))
            ->when($paidStatus, fn($query) => $query->where("paid_status", $paidStatus))
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("phone_number", "like", "%$searchKey%")
                    ->orWhere("customer_name", "like", "%$searchKey%")
                    ->orWhere("id", "like", "%$searchKey%");
            })
            ->where("created_by", Auth::id())
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

        return $orders;
    }

    public function customerShow($id)
    {
        $order = $this->model->with([
            "deliveryGateway:id,name",
            "paymentGateway:id,name",
            "coupon:id,name,code",
            "currentStatus:id,name,bg_color,text_color",
            "statuses:id,name,bg_color,text_color",
            "details",
            "details.attributeValue1:id,value",
            "details.attributeValue2:id,value",
            "details.attributeValue3:id,value",
            "details.product:id,name,img_path"
        ])
            ->find($id);

        if (!$order) {
            throw new CustomException("Order not found");
        }

        return $order;
    }

    public function updateStatus($request)
    {
        DB::transaction(function () use ($request) {
            $steadFastOrderIds   = [];
            $pathaoOrderIds      = [];
            $courierNotAttachIds = [];

            $orders = $this->model->whereIn("id", $request->order_ids)->get();

            foreach ($orders as $order) {
                if ($request->current_status_id == $order->current_status_id) {
                    throw new CustomException("This status already attach with order id $order->id");
                }

                if (Helper::setting("is_stock_maintain") && $request->current_status_id == OrderStatusEnum::CANCELED) {
                    // Check order is approved
                    $checkOrderIsApproved = $order->statuses->contains('id', OrderStatusEnum::APPROVED);

                    // Add previous quantity with product current stock
                    if ($checkOrderIsApproved) {
                        foreach ($order->details as $item) {
                            $product = Product::with(["variations"])->find($item->product_id);

                            if ($product) {
                                if ($product->variations->isNotEmpty()) {
                                    $variation = ProductVariation::where("product_id", $item->product_id)
                                        ->where("attribute_value_id_1", $item->attribute_value_id_1)
                                        ->where("attribute_value_id_2", $item->attribute_value_id_2)
                                        ->where("attribute_value_id_3", $item->attribute_value_id_3)
                                        ->first();

                                    if (!$variation) {
                                        throw new CustomException("Variation product not found ");
                                    }

                                    $variation->current_stock  += $item->quantity;
                                    $variation->total_sell_qty -= $item->quantity;
                                    $variation->save();
                                } else {
                                    $product->current_stock  += $item->quantity;
                                    $product->total_sell_qty -= $item->quantity;
                                    $product->save();
                                }
                            }
                        }
                    }
                }

                if (Helper::setting("is_stock_maintain") && $request->current_status_id == OrderStatusEnum::APPROVED) {
                    foreach ($order->details as $item) {
                        $product = Product::with(["variations"])->find($item->product_id);

                        if (!$product) {
                            throw new CustomException("Product not found ");
                        }

                        if ($product->variations->isNotEmpty()) {
                            $variation = ProductVariation::where("product_id", $item->product_id)
                                ->where("attribute_value_id_1", $item->attribute_value_id_1)
                                ->where("attribute_value_id_2", $item->attribute_value_id_2)
                                ->where("attribute_value_id_3", $item->attribute_value_id_3)
                                ->first();

                            if (!$variation) {
                                throw new CustomException("Variation product not found ");
                            }

                            $attributeValue1 = @$variation->attributeValue1->value ?? null;
                            $attributeValue2 = @$variation->attributeValue2->value ?? null;
                            $attributeValue3 = @$variation->attributeValue3->value ?? null;

                            // Check negative sell allow
                            if (!Helper::setting("is_negative_stock_allow")) {
                                if ($variation->current_stock < $item->quantity) {
                                    throw new CustomException("$product->name $attributeValue1 $attributeValue2 $attributeValue3 out of stock");
                                }
                            }

                            $variation->current_stock  -= $item->quantity;
                            $variation->total_sell_qty += $item->quantity;
                            $variation->save();
                        } else {
                            // Check product stock
                            if (!Helper::setting("is_negative_stock_allow")) {
                                if ($product && $product->current_stock < $item->quantity) {
                                    throw new CustomException("$product->name out of stock");
                                }
                            }

                            $product->current_stock  -= $item->quantity;
                            $product->total_sell_qty += $item->quantity;
                            $product->save();
                        }
                    }
                }

                if ($request->current_status_id == 3) {
                    if ($request->filled('approx_start_date') || $request->filled('approx_end_date')) {

                        FollowUp::updateOrCreate(
                            [
                                'order_id' => $order->id,
                            ],
                            [
                                'start_date' => $request->approx_start_date,
                                'end_date'   => $request->approx_end_date,
                                'note'       => $request->follow_note,
                            ]
                        );
                    }
                }

                // Check current status on the way
                if ($request->current_status_id == OrderStatusEnum::ON_THE_WAY) {
                    if ($order->courier_id == 1) {
                        $steadFastOrderIds[] = $order->id;
                    } elseif ($order->courier_id == 2) {
                        $pathaoOrderIds[] = $order->id;
                    } elseif ($order->courier_id == 3) {
                        $request->merge(["order_id" => $order->id]);
                        (new RedxRepository)->parcelCreate($request);
                    } else {
                        $courierNotAttachIds[] = $order->id;
                        continue;
                    }
                }

                $order->current_status_id = $request->current_status_id;
                $order->cancel_reason_id  = $request->cancel_reason_id ?? null;
                $order->save();

                // attach order status id
                $order->statuses()->syncWithoutDetaching([$request->current_status_id]);
            }

            if (count($steadFastOrderIds) > 0) {
                $request->merge(["order_ids" => $steadFastOrderIds]);

                (new SteadFastRepository)->bulkCreate($request);
            }

            if (count($pathaoOrderIds) > 0) {
                $request->merge(["order_ids" => $pathaoOrderIds]);

                (new PathaoRepository)->createBulkOrder($request);
            }

            if (count($courierNotAttachIds) > 0) {
                $ids = implode(", ", $courierNotAttachIds);

                return "Courier not assign with order ids: $ids";
            }

            return true;
        });
    }

    public function sendCourier($request)
    {
        return DB::transaction(function () use ($request) {
            $currentStatusId = OrderStatusEnum::ON_THE_WAY;

            $order = $this->model->find($request->order_id);

            if (!$order) {
                throw new CustomException("Order not found ");
            }

            if ($order->consignment_id) {
                throw new CustomException("This order already sent to courier");
            }

            if ($order->courier_id == 1) { // Steadfast
                $request->merge(["order_id" => $order->id]);
                (new SteadFastRepository)->createOrder($request);
            } elseif ($order->courier_id == 2) { // Pathao
                $request->merge(["order_id" => $order->id]);
                (new PathaoRepository)->createOrder($request);
            } elseif ($order->courier_id == 3) { // Redex
                $request->merge(["order_id" => $order->id]);
                (new RedxRepository)->parcelCreate($request);
            } else {
                throw new CustomException("Courier not assign with this order");
            }

            $order->current_status_id = $currentStatusId;
            $order->save();

            // attach order status id
            $order->statuses()->syncWithoutDetaching([$currentStatusId]);

            return true;
        });
    }

    public function updatePaidStatus($request)
    {
        foreach ($request->order_ids as $id) {
            $order = $this->model->find($id);

            if (!$order) {
                throw new CustomException("Order $id not found ");
            }

            $order->paid_status = $request->paid_status;
            $order->save();
        }

        return true;
    }

    public function addAdditionCost($request)
    {
        $averageCost = 0;
        $endDate     = $request->end_date ?? $request->start_date;
        $startDate   = Helper::startOfDate($request->start_date);
        $endDate     = Helper::endOfDate($endDate);

        $orders = $this->model->whereBetween('created_at', [$startDate, $endDate])->get();

        if (count($orders) > 0) {
            $averageCost = $request->cost / $orders->count();

            foreach ($orders as $order) {
                $order->additional_cost = $averageCost;
                $order->save();
            }
        } else {
            throw new CustomException("Order not found");
        }

        return true;
    }

    public function multipleInvoice($request)
    {
        $startDate = $request->input('start_date', null);
        $endDate   = $request->input('end_date', null);
        $orderIds  = $request->input('order_ids', []);

        $orders = $this->model->with([
            "createdBy:id,username",
            "updatedBy:id,username",
            "preparedBy:id,username",
            "deliveryGateway:id,name",
            "paymentGateway:id,name",
            "coupon:id,name,code",
            "currentStatus:id,name,bg_color,text_color",
            "statuses:id,name,bg_color,text_color",
            "details",
            "details.attributeValue1:id,value",
            "details.attributeValue2:id,value",
            "details.attributeValue3:id,value",
            "details.product:id,name",
            "details.product.variations",
        ]);

        if ($startDate && $endDate) {
            $startDate = Helper::startOfDate($startDate);
            $endDate   = Helper::endOfDate($endDate);

            $orders = $orders->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($orderIds) {
            $orders = $orders->whereIn('id', $orderIds);
        }

        if (($startDate && $endDate) || $orderIds) {
            $query = clone $orders;

            $orders->update(["is_invoice_printed" => true]);

            $orders = $query->get();
        } else {
            $orders = [];
        }

        return $orders;
    }

    public function orderHistory($request, $id)
    {
        $limit = $request->input("limit", 10);

        return $this->model->find($id)->audits()->with("user:id,username")->take($limit)->get();
    }

    public function trashList($request)
    {
        $searchKey       = $request->input('search_key', null);
        $currentStatusId = $request->input('current_status_id', null);
        $paidStatus      = $request->input('paid_status', null);
        $orderFrom       = $request->input('order_from', null);
        $startDate       = $request->input('start_date', null);
        $endDate         = $request->input('end_date', null);
        $paginateSize    = Helper::checkPaginateSize($request);

        $orders = $this->model->onlyTrashed()->with([
            'currentStatus',
            'paymentGateway:id,name',
            'preparedBy:id,username',
            'updatedBy:id,username'
        ]);

        $orders = $orders->when($currentStatusId, fn($query) => $query->where("current_status_id", $currentStatusId))
            ->when($currentStatusId, fn($q) => $q->where("current_status_id", $currentStatusId))
            ->when($paidStatus, fn($q) => $q->where("paid_status", $paidStatus))
            ->when($orderFrom, fn($q) => $q->where("order_from", $orderFrom))

            ->when($startDate, fn($q) =>
                $q->whereDate('deleted_at', '>=', $startDate)
            )

            ->when($endDate, fn($q) =>
                $q->whereDate('deleted_at', '<=', $endDate)
            )

            ->when($searchKey, function ($q) use ($searchKey) {
                $q->where(function ($sub) use ($searchKey) {
                    $sub->where("phone_number", "like", "%$searchKey%")
                        ->orWhere("customer_name", "like", "%$searchKey%")
                        ->orWhere("invoice_number", "like", "%$searchKey%")
                        ->orWhere("id", "like", "%$searchKey%");
                });
            })

            ->orderBy("deleted_at", "desc")
            ->paginate($paginateSize);

        return $orders;
    }

    public function restore($id)
    {
        $order = $this->model->onlyTrashed()->find($id);

        if (!$order) {
            throw new CustomException("Order not found", 404);
        }

        $order->restore();

        return $order;
    }

    public function permanentDelete($id)
    {
        $order = $this->model->onlyTrashed()->find($id);

        if (!$order) {
            throw new CustomException("Order not found");
        }

        $order->details()->delete();
        $order->statuses()->detach();

        return $order->forceDelete();
    }

    public function districtWiseOrderCount()
    {
        $districtWiseOrderCount = $this->model
            ->join("districts", "orders.district_id", "=", "districts.id")
            ->select(
                "districts.id as id",
                "districts.name as name",
                DB::raw("COUNT(orders.id) as orders_count"),
                DB::raw("SUM(orders.payable_price) as total_amount")
            )
            ->where("orders.current_status_id", OrderStatusEnum::APPROVED)
            ->groupBy("districts.id", "districts.name")
            ->get();

        return $districtWiseOrderCount;
    }

    public function searchByPhoneNumber($request)
    {
        $phoneNumber = $request->input("phone_number", null);

        $orders = $this->model
            ->select(
                "id",
                "courier_id",
                "district_id",
                "customer_type_id",
                "delivery_charge",
                "phone_number",
                "customer_name",
                "address_details",
                "pickup_store_id",
                "delivery_type",
                "courier_area_id",
                "delivery_area",
                "item_weight"
            )
            ->where("phone_number", "like", "%$phoneNumber%")
            ->latest()
            ->take(10)
            ->get();

        return $orders;
    }

    public function pikingList()
    {
        $orderDetails = OrderDetail::with([
            "attributeValue1:id,value,attribute_id",
            "attributeValue2:id,value,attribute_id",
            "attributeValue3:id,value,attribute_id",
            "attributeValue1.attribute:id,name",
            "attributeValue2.attribute:id,name",
            "attributeValue3.attribute:id,name",
            "product:id,name,img_path,current_stock"
        ])
            ->select([
                "product_id",
                "attribute_value_id_1",
                "attribute_value_id_2",
                "attribute_value_id_3",
                DB::raw("SUM(quantity) as quantity"),
                DB::raw("SUM(buy_price) as buy_price"),
                DB::raw("SUM(mrp) as mrp"),
                DB::raw("SUM(discount) as discount"),
                DB::raw("SUM(sell_price) as sell_price")
            ])
            ->whereHas("order", fn($query) => $query->where("current_status_id", OrderStatusEnum::APPROVED))
            ->groupBy("product_id", "attribute_value_id_1", "attribute_value_id_2", "attribute_value_id_3")
            ->get();

        return $orderDetails;
    }

    public function itemList($request)
    {
        $orderDetails = OrderDetail::with([
            "attributeValue1:id,value,attribute_id",
            "attributeValue2:id,value,attribute_id",
            "attributeValue3:id,value,attribute_id",
            "attributeValue1.attribute:id,name",
            "attributeValue2.attribute:id,name",
            "attributeValue3.attribute:id,name",
            "product:id,name,current_stock,img_path",
            "product.variations",
        ])
            ->where("order_id", $request->order_id)
            ->get();

        return $orderDetails;
    }

    // ================================== Start sub function =============================================
    private function checkUserIsBlock($request)
    {
        $orderCount = 0;
        $orderGuard = OrderGuard::where("status", StatusEnum::ACTIVE)->first();

        if (!$orderGuard) {
            return;
        }

        // Update block status if condition is not satisfied
        $userBlock = BlockUser::with(['details'])
        ->where(function ($query) use ($request) {
            $query->where("user_token", $request->user_token)
                ->orWhereHas("details", function ($query) use ($request) {
                    $query->where("phone_number", $request->phone_number)
                    ->orWhere("ip_address", $request->ip());
                });
        })->first();

        if (!$userBlock) {
            return;
        }

        $orderCount = $this->model
        ->where("block_user_id", $userBlock->id)
        ->whereBetween("created_at", Helper::getStartAndEndTime($orderGuard->duration, $orderGuard->duration_type))
        ->count();

        if ($orderCount < $orderGuard->quantity && !$userBlock->is_permanent_unblock) {
            $userBlock->update(["is_block" => 0]);
        }

        if (!$userBlock->is_permanent_unblock) {
            if($userBlock->is_permanent_block){
                throw new CustomException($orderGuard->permanent_block_message);
            }else if($userBlock->is_block){
                throw new CustomException($orderGuard->block_message);
            }
        }

        // User block if condition is satisfied
        if (($orderCount + 1 >= $orderGuard->quantity && !$userBlock->is_permanent_unblock)) {
            $userBlock->update(["is_block" => 1]);
        }
    }

    private function addUseBlock($request)
    {
        $userBlock = BlockUser::with(['details'])
            ->where(function ($query) use ($request) {
                $query->where("user_token", $request->user_token)
                    ->orWhereHas("details", function ($query) use ($request) {
                        $query->where("phone_number", $request->phone_number);
                    });
            })
            ->first();

        $agent = new Agent();

        $deviceType = $agent->isDesktop() ? 'Desktop' : ($agent->isMobile() ? 'Mobile' : 'Tablet');

        if ($userBlock) {
            // Store user token  details
            $userBlock->details()->updateOrCreate(
                [
                    "phone_number" => $request->phone_number,
                    "ip_address"   => $request->ip(),
                ],
                [
                    "browser"      => $agent->browser(),
                    "platform"     => $agent->platform(),
                    "device_type"  => $deviceType,
                ]
            );
        } else {
            $userBlock = new BlockUser();

            $userBlock->user_token = $request->user_token;
            $userBlock->save();

            $userBlock->details()->create(
                [
                    "phone_number" => $request->phone_number,
                    "ip_address"   => $request->ip(),
                    "browser"      => $agent->browser(),
                    "platform"     => $agent->platform(),
                    "device_type"  => $deviceType,
                ]
            );
        }

        return $userBlock;
    }

    private function checkBonusPointApplicable($request)
    {
        if (Auth::check() && $request->is_bonus_point_applied) {
            $user            = User::find(Auth::id());
            $bonusPointValue = Helper::setting("bonus_point_value");

            if ($bonusPointValue && $user->bonus_points && ($user->bonus_points >= $bonusPointValue)) {
                $discountValue = $user->bonus_points / $bonusPointValue;

                // Update bonus point
                $user->bonus_points -= floor($discountValue * $bonusPointValue);
                $user->save();

                return round($discountValue);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    private function addBonusPoint($order)
    {
        if (Auth::check() && Helper::setting("is_bonus_point_add")) {
            $netOrderPrice = $order->getNetOrderPrice();

            $user = User::find(Auth::id());
            $user->bonus_points += $netOrderPrice;
            $user->save();
        }
    }

    private function checkIsFreeDeliveryApplicable($order)
    {
        $freeDelivery = FreeDelivery::where("status", StatusEnum::ACTIVE)->first();

        if (!$freeDelivery) {
            return false;
        }

        $meetsQuantityCondition = $freeDelivery->quantity > 0 && $order->getTotalQuantity() >= $freeDelivery->quantity;
        $meetsPriceCondition = $freeDelivery->price > 0 && $order->getNetOrderPrice() >= $freeDelivery->price;

        if ($meetsQuantityCondition || $meetsPriceCondition) {
            $order->delivery_charge = 0;
            $order->save();
        }
    }

    private function onlinePaymentDiscount($paymentGatewayId, $order)
    {
        $onlinePaymentDiscount = OnlinePaymentDiscount::query()
            ->where("payment_gateway_id", $paymentGatewayId)
            ->where("status", StatusEnum::ACTIVE)
            ->first();

        if ($onlinePaymentDiscount) {
            $netOrderAmount = $order->getNetOrderPrice();
            if ($netOrderAmount >= $onlinePaymentDiscount->minimum_cart_amount) {
                if ($onlinePaymentDiscount->discount_type === DiscountTypeEnum::PERCENTAGE) {
                    $onlinePaymentDiscountValue = $onlinePaymentDiscount->discount_amount * $netOrderAmount / 100;

                    //    Check maximum discount amount
                    if ($onlinePaymentDiscountValue > $onlinePaymentDiscount->maximum_discount_amount) {
                        $onlinePaymentDiscountValue = $onlinePaymentDiscount->maximum_discount_amount;
                    }
                } else {
                    $onlinePaymentDiscountValue =  $onlinePaymentDiscount->discount_amount;
                }

                $order->special_discount += round($onlinePaymentDiscountValue);
                $order->save();
            }
        }
    }

    // Check duplicate order
    private function isDuplicateOrder($request)
    {
        $startTime = now()->subHour(Helper::setting("duplicate_order_check_duration"));
        $endTime   = now();

        return $this->model
            ->where("phone_number", $request->phone_number)
            ->whereBetween("created_at", [$startTime, $endTime])
            ->exists();
    }

    public function updateInvoice($id)
    {
        $order = $this->model->find($id);

        if (!$order) {
            throw new CustomException("Order not found");
        }

        $order->update(['is_invoice_printed' => true]);

        return $order;
    }
    // ================================== End sub function =============================================
}
