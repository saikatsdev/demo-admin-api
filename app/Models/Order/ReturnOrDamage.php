<?php

namespace App\Models\Order;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnOrDamage extends BaseModel
{
    public function details(): HasMany
    {
        return $this->hasMany(ReturnOrDamageDetail::class, "return_or_damage_id", "id");
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, "order_id", "id");
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, "status_id", "id");
    }

    // =================================== Helper function ==========================================

    // Calculate order total buy price
    public function getTotalBuyPrice()
    {
        $totalBuyPrice = $this->details->sum(function ($item) {
            $itemBuyPrice = $item->buy_price;
            $itemQty      = $item->quantity;

            return $itemBuyPrice * $itemQty;
        });

        return $totalBuyPrice;
    }

    // Calculate items total mrp
    public function getTotalMRP()
    {
        $totalMRP = $this->details->sum(function ($item) {
            $itemMRP = $item->mrp;
            $itemQty = $item->quantity;

            return $itemMRP * $itemQty;
        });

        return $totalMRP;
    }

    // Get total items discount
    public function getTotalDiscount()
    {
        $totalDiscount = $this->details->sum(function ($item) {
            $itemDiscount = $item->discount;
            $itemQty      = $item->quantity;

            return $itemDiscount * $itemQty;
        });

        return $totalDiscount;
    }

    // Calculate items total MRP
    public function getTotalSellPrice()
    {
        $totalSellPrice = $this->details->sum(function ($item) {
            $itemSellPrice = $item->sell_price;
            $itemQty       = $item->quantity;

            return $itemSellPrice * $itemQty;
        });

        return round($totalSellPrice);
    }


    public function updateOrderValue($order)
    {
        $totalBuyPrice  = $order->getTotalBuyPrice() ?? 0;
        $totalMRP       = $order->getTotalMRP() ?? 0;
        $totalDiscount  = $order->getTotalDiscount() ?? 0;
        $totalSellPrice = $order->getTotalSellPrice() ?? 0;

        $order->buy_price       = $totalBuyPrice;
        $order->mrp             = $totalMRP;
        $order->discount        = $totalDiscount;
        $order->sell_price      = $totalSellPrice;
        $order->save();
    }
}
