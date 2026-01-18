<?php

namespace App\Repositories\Order;

use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use App\Models\Order\Coupon;
use App\Exceptions\CustomException;

class CouponRepository
{
    public function __construct(protected Coupon $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);

        $coupons = $this->model->with(["createdBy:id,username"])
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", $searchKey);
        })
        ->orderBy("created_at", "desc")
        ->paginate($paginateSize);

        return $coupons;
    }

    public function store($request)
    {
        $coupon = new $this->model();

        $coupon->name            = $request->name;
        $coupon->code            = $request->code;
        $coupon->status          = $request->status;
        $coupon->discount_type   = $request->discount_type;
        $coupon->discount_amount = $request->discount_amount;
        $coupon->min_cart_amount = $request->min_cart_amount;
        $coupon->started_at      = $request->started_at;
        $coupon->ended_at        = $request->ended_at;
        $coupon->description     = $request->description;
        $coupon->save();

        return $coupon;
    }

    public function show($id)
    {
        $coupon = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$coupon) {
            throw new CustomException("Coupon not found");
        }

        return $coupon;
    }

    public function update($request, $id)
    {
        $coupon = $this->model->find($id);

        if (!$coupon) {
            throw new CustomException("Coupon Not found");
        }

        $coupon->name            = $request->name;
        $coupon->code            = $request->code;
        $coupon->status          = $request->status;
        $coupon->min_cart_amount = $request->min_cart_amount;
        $coupon->discount_type   = $request->discount_type;
        $coupon->discount_amount = $request->discount_amount;
        $coupon->started_at      = $request->started_at;
        $coupon->ended_at        = $request->ended_at;
        $coupon->description     = $request->description;
        $coupon->save();

        return $coupon;
    }

    public function getCoupon()
    {
        $now = Carbon::now();

        $coupon = Coupon::where("status", "active")
        ->whereDate("started_at", "<=", $now)
        ->whereDate("ended_at", ">=", $now)
        ->first();

        return $coupon;
    }

    public function delete($id)
    {
        $coupon = $this->model->find($id);

        if (!$coupon) {
            throw new CustomException('Coupon not found');
        }

        return $coupon->delete();
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey = $request->input('search_key', null);

        $coupons = $this->model->with(["createdBy:id,username"])
        ->onlyTrashed()
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", $searchKey);
        })
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $coupons;
    }

    public function restore($id)
    {
        $coupon = $this->model->onlyTrashed()->find($id);

        if (!$coupon) {
            throw new CustomException('Coupon not found');
        }

        $coupon->restore();

        return $coupon;
    }

    public function permanentDelete($id)
    {
        $coupon = $this->model->onlyTrashed()->find($id);

        if (!$coupon) {
            throw new CustomException('Coupon not found');
        }

        return $coupon->forceDelete();
    }

    public function checkCouponCode($request)
    {
        $now             = Carbon::now();
        $couponCode      = $request->input("coupon_code", null);
        $couponCode      = Str::of($couponCode)->trim();
        $cartTotalAmount = $request->input("cart_total_amount", 0);

        $coupon = Coupon::where("code", $couponCode)
        ->where("status", "active")
        ->whereDate("started_at", "<=", $now)
        ->whereDate("ended_at", ">=", $now)
        ->first();

        if (!$coupon) {
            throw new CustomException("Invalid coupon code");
        }

        $minCartValue = $coupon->min_cart_amount;

        if ($cartTotalAmount < $minCartValue) {
            throw new CustomException("Minimum cart amount without delivery charge {$minCartValue} is required");
        }

        // Calculate discount amount
        $discountAmount = $coupon->discount_type === "fixed" ? $coupon->discount_amount : ($cartTotalAmount * $coupon->discount_amount) / 100;

        $data = [
            "discount_amount" => $discountAmount,
            "coupon_id"       => $coupon->id,
            "name"            => $coupon->name,
            "status"          => $coupon->status,
            "code"            => $coupon->code,
            "discount_type"   => $coupon->discount_type,
        ];

        return $data;
    }
}
