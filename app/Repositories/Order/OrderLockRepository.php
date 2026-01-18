<?php

namespace App\Repositories\Order;

use Carbon\Carbon;
use App\Helpers\Helper;
use App\Models\Order\Order;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;

class OrderLockRepository
{
    protected $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function orderLockedStatus($id)
    {
        $order = $this->model->select("id", "locked_by_id")->with(["lockedBy:id,username"])->find($id);

        if (!$order) {
            throw new CustomException("Order not found");
        }

        $lockStatus = false;
        if ($order->locked_by_id && $order->locked_by_id != Auth::id()) {
            $lockedAt      = Carbon::parse($order->locked_at);
            $diffInMinutes = $lockedAt->diffInMinutes(now());

            // Check if lock duration
            if ($diffInMinutes < Helper::setting("order_locked_duration")) {
                $lockStatus = true;
            }
        }

        // Prepare response
        $response = [
            'order_id'     => $order->id,
            'locked_by_id' => $order->locked_by_id,
            'locked_by'    => optional($order->lockedBy)->username,
            'lock_status'  => $lockStatus,
            'locked_at'    => $order->locked_at,
        ];

        return $response;
    }

    public function orderLocked($id)
    {
        $order = $this->model->select("id", "locked_by_id")->find($id);

        if (!$order) {
            throw new CustomException("Order not found");
        }

        $order->locked_by_id = Auth::id();
        $order->locked_at    = now();
        $order->save();

        $order->load("lockedBy:id,username");

        $response = [
            'order_id'     => $order->id,
            'locked_by_id' => $order->locked_by_id,
            'locked_by'    => optional($order->lockedBy)->username,
            'locked_at'    => $order->locked_at,
        ];

        return $response;
    }

    public function orderUnlocked($id)
    {
        $order = $this->model->select("id", "locked_by_id")->find($id);

        if (!$order) {
            throw new CustomException("Order not found");
        }

        $order->locked_by_id = null;
        $order->locked_at    = null;;
        $order->save();

        $response = [
            'order_id'     => $order->id,
            'locked_by_id' => $order->locked_by_id,
            'locked_by'    => optional($order->lockedBy)->username,
            'locked_at'    => $order->locked_at,
        ];

        return $response;
    }
}
