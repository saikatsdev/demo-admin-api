<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\OrderStatusEnum;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Models\Order\IncompleteOrder;

class IncompleteOrderRepository
{
    public function __construct(protected IncompleteOrder $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key");
        $statusId     = $request->input("status_id");
        $startDate    = $request->input("start_date");
        $endDate      = $request->input("end_date");
        $trash        = $request->input("trash");

        $baseQuery = $this->model->query()
            ->when($trash, fn($q) => $q->onlyTrashed())
            ->when(!$trash, fn($q) => $q->whereNull("deleted_at"))
            ->when($searchKey, fn($q) => $q->where("phone_number", "like", "%{$searchKey}%"))
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween("created_at", [
                    $startDate . ' 00:00:00',
                    $endDate   . ' 23:59:59'
                ]);
            });

        $summary = (clone $baseQuery)
            ->selectRaw("COUNT(*) as total_orders")
            ->selectRaw("SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as total_pending")
            ->selectRaw("SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) as total_approved")
            ->selectRaw("SUM(CASE WHEN status_id = 8 THEN 1 ELSE 0 END) as total_cancelled")
            ->first();

        $orders = (clone $baseQuery)
            ->with([
                "details",
                "details.product:id,name,slug,buy_price,img_path,mrp,offer_price,discount,sell_price,offer_percent,current_stock",
                "details.attributeValue1:id,value,attribute_id",
                "details.attributeValue2:id,value,attribute_id",
                "details.attributeValue3:id,value,attribute_id",
                "details.attributeValue1.attribute:id,name",
                "details.attributeValue2.attribute:id,name",
                "details.attributeValue3.attribute:id,name",
                "status",
                "createdBy:id,username",
                "status:id,name,text_color,bg_color",
            ])
            ->when(!$trash, fn($q) => $q->whereNull("deleted_at"))
            ->when($statusId, fn($q) => $q->where("status_id", $statusId))
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

        return [
            'orders'  => $orders,
            'summary' => $summary,
        ];
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $incompleteOrder = $this->model::updateOrCreate(
                [
                    "phone_number" => $request->phone_number
                ],
                [
                    "name"       => $request->name,
                    "address"    => $request->address,
                    "ip_address" => $request->ip(),
                    "status_id"  => OrderStatusEnum::PENDING,
                ]
            );

            foreach ($request->items as $item) {
                $product = Product::find($item["product_id"]);

                if($product){
                    $product->increment("incomplete_order_count");
                }

                $incompleteOrder->details()->firstOrCreate([
                    "incomplete_order_id"  => $incompleteOrder->id,
                    "product_id"           => $item["product_id"],
                    "attribute_value_id_1" => @$item["attribute_value_id_1"],
                    "attribute_value_id_2" => @$item["attribute_value_id_2"],
                    "attribute_value_id_3" => @$item["attribute_value_id_3"]
                ]);
            }

            return $incompleteOrder;
        });
    }

    public function show($id)
    {
        $incompleteOrder = $this->model->with([
            "details",
            "details.product:id,name,slug,buy_price,mrp,offer_price,discount,sell_price,offer_percent,current_stock",
            "details.attributeValue1:id,value,attribute_id",
            "details.attributeValue2:id,value,attribute_id",
            "details.attributeValue3:id,value,attribute_id",
            "details.attributeValue1.attribute:id,name",
            "details.attributeValue2.attribute:id,name",
            "details.attributeValue3.attribute:id,name",
            "status",
            "createdBy:id,username",
            "updatedBy:id,username"
        ])->find($id);

        if (!$incompleteOrder) {
            throw new CustomException("Incomplete order not found");
        }

        return $incompleteOrder;
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $incompleteOrder = $this->model->find($id);

            if (!$incompleteOrder) {
                throw new CustomException("incomplete not found");
            }

            $incompleteOrder->name         = $request->name;
            $incompleteOrder->phone_number = $request->phone_number;
            $incompleteOrder->address      = $request->address;
            $incompleteOrder->ip_address   = $request->ip_address;
            $incompleteOrder->status_id    = $request->status_id;
            $incompleteOrder->save();

            $incompleteOrderDetails = [];
            foreach ($request->items as $item) {
                $incompleteOrderDetails[] = [
                    "incomplete_order_id"  => $incompleteOrder->id,
                    "product_id"           => $item["product_id"],
                    "attribute_value_id_1" => @$item["attribute_value_id_1"],
                    "attribute_value_id_2" => @$item["attribute_value_id_2"],
                    "attribute_value_id_3" => @$item["attribute_value_id_3"],
                    "note"                 => @$item["note"],
                    "created_at"           => now(),
                ];
            }

            $incompleteOrder->details()->delete();
            $incompleteOrder->details()->insert($incompleteOrderDetails);

            return $incompleteOrder;
        });
    }

    public function delete($id)
    {
        $incompleteOrder = $this->model->find($id);

        if (!$incompleteOrder) {
            throw new CustomException("Incomplete order not found");
        }

        $incompleteOrder->details()->delete();

        $incompleteOrder->delete();

        return $incompleteOrder;
    }
    
    public function trashed($request)
    {
        $trashed = $this->model::onlyTrashed()->with(['details'])->get();

        return $trashed;
    }
    
    public function restore($id)
    {
        $order = $this->model::onlyTrashed()->where("id", $id)->first();

        if(!$order){
            throw new CustomException("Order Not Found");
        }

        $order->restore();

        return $order;
    }
}
