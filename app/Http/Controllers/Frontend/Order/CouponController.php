<?php

namespace App\Http\Controllers\Frontend\Order;

use Exception;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\CouponRepository;
use App\Http\Resources\Frontend\Order\CouponResource;
use App\Http\Requests\Frontend\CheckCouponCodeRequest;

class CouponController extends BaseController
{
    public function __construct(protected CouponRepository $repository) {}

    public function getCoupon()
    {
        try {
            $coupon = $this->repository->getCoupon();

            return $this->sendResponse($coupon, 'Coupon list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function checkCouponCode(CheckCouponCodeRequest $request)
    {
        try {
            $data = $this->repository->checkCouponCode($request);

            $data = new CouponResource($data);

            return $this->sendResponse($data, "Coupon discount amount", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
