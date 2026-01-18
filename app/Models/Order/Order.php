<?php

namespace App\Models\Order;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\StatusEnum;
use App\Models\BaseModel;
use App\Models\Product\Product;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $casts  = [
        "callback_response" => "array",
    ];

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, "order_id", "id");
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, "order_statuses", "order_id", "status_id")
            ->orderBy("order_statuses.created_at", "asc")
            ->withTimestamps();
    }

    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, "payment_gateway_id", "id");
    }

    public function deliveryGateway(): BelongsTo
    {
        return $this->belongsTo(DeliveryGateway::class, "delivery_gateway_id", "id");
    }

    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, "current_status_id", "id");
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, "coupon_id", "id");
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "updated_by", "id");
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "prepared_by", "id");
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "locked_by_id", "id");
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, "order_id", "id");
    }

    public function notes(): HasMany
    {
        return $this->hasMany(OrderNote::class, "order_id", "id");
    }

    public function orderFrom(): BelongsTo
    {
        return $this->belongsTo(OrderFrom::class, "order_from_id", "id");
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class, "courier_id", "id");
    }

    public function courierStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, "courier_status_id", "id");
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, "district_id", "id");
    }

    public function assignUser(): BelongsTo
    {
        return $this->belongsTo(User::class, "assign_user_id", "id");
    }

    public function customerType(): BelongsTo
    {
        return $this->belongsTo(CustomerType::class, "customer_type_id", "id");
    }

    public function cancelReason(): BelongsTo
    {
        return $this->belongsTo(CancelReason::class, "cancel_reason_id", "id");
    }

    // ========================================== Scope ===================================================================
    public function scopeToday($query)
    {
        return $query->whereDate("created_at", Carbon::today()->toDateString());
    }

    // Scope for this month"s orders
    public function scopeThisMonth($query)
    {
        return $query->whereMonth("created_at", Carbon::now()->month)->whereYear("created_at", Carbon::now()->year);
    }

    // Scope for this year"s orders
    public function scopeThisYear($query)
    {
        return $query->whereYear("created_at", Carbon::now()->year);
    }

    // ================================================== Helper function ================================================
    public function getCouponValue()
    {
        $couponAmount = 0;

        if ($this->coupon_id) {
            $coupon = Coupon::find($this->coupon_id);
            if ($coupon) {
                if ($coupon->discount_type == "fixed") {
                    $couponAmount = $coupon->discount_amount;
                } else {
                    $itemsSubtotalAmount = $this->getTotalSellPrice();
                    $couponPercent = $coupon->discount_amount;
                    $couponAmount = ($couponPercent * $itemsSubtotalAmount) / 100;
                }
            }
        }

        return $couponAmount;
    }

    // Calculate order total quantity
    public function getTotalQuantity()
    {
        $totalQuantity = $this->details->sum(function ($item) {
            return $item->quantity;
        });

        return $totalQuantity;
    }

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

        return $totalSellPrice;
    }

    public function getNetOrderPrice()
    {
        $totalSellPrice = $this->getTotalSellPrice();
        $couponDiscount = $this->getCouponValue();

        $netOrderPrice = $totalSellPrice - ($couponDiscount + $this->special_discount);

        return $netOrderPrice;
    }

    public function getPayablePrice($roundType = null)
    {
        $netOrderValue = $this->getNetOrderPrice();

        $payablePrice = $netOrderValue + $this->delivery_charge;

        if ($roundType === "ceil") {
            return ceil($payablePrice);
        } else if ($roundType === "floor") {
            return floor($payablePrice);
        } else if ($roundType === "round") {
            return round($payablePrice);
        } else {
            return $payablePrice;
        }
    }

    public function updateOrderValue($order)
    {
        $totalBuyPrice  = $order->getTotalBuyPrice() ?? 0;
        $totalMRP       = $order->getTotalMRP() ?? 0;
        $totalSellPrice = $order->getTotalSellPrice() ?? 0;
        $couponValue    = $order->getCouponValue() ?? 0;
        $totalDiscount  = $order->getTotalDiscount() ?? 0;
        $netOrderPrice  = $order->getNetOrderPrice() ?? 0;
        $payablePrice   = $order->getPayablePrice("round") ?? 0;
        $due            = $payablePrice - $this->advance_payment;

        if ($order->paid_status === StatusEnum::PAID) {
            $due = 0;
        }

        $order->coupon_value    = $couponValue;
        $order->buy_price       = $totalBuyPrice;
        $order->mrp             = $totalMRP;
        $order->sell_price      = $totalSellPrice;
        $order->discount        = $totalDiscount;
        $order->net_order_price = $netOrderPrice;
        $order->payable_price   = $payablePrice;
        $order->due             = $due;
        $order->save();
    }

    public function updateItemStock($order, $statusId = null)
    {
        if ($statusId != 5 && $statusId != 9) {
            if (count($order->items)) {
                foreach ($order->items as $item) {
                    $productId = $item->id;
                    $quantity  = $item->pivot->quantity;

                    $product = Product::find($productId);
                    if ($product) {
                        $currentStock = $product->current_stock;
                        $currentStock = $currentStock - $quantity;
                        $product->current_stock = $currentStock;
                        $product->save();
                    }
                }
            }
        }
    }
}
