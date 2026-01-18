<?php

namespace App\Http\Controllers\Frontend\Order;

use Exception;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\OnlinePaymentDiscountRepository;
use App\Http\Resources\Frontend\Order\OnlinePaymentDiscountCollection;

class OnlinePaymentDiscountController extends BaseController
{
    public function __construct(protected OnlinePaymentDiscountRepository $repository) {}

    public function index(Request $request)
    {
        try {
            $request->merge(["status" => StatusEnum::ACTIVE]);

            $discounts = $this->repository->index($request);

            $discounts = new OnlinePaymentDiscountCollection($discounts);

            return $this->sendResponse($discounts, 'Online payment discount list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
