<?php

namespace App\Repositories\Order;

use Carbon\Carbon;
use App\Helpers\Helper;
use App\Models\Order\FollowUp;

class FollowupRepository
{
    public function __construct(protected FollowUp $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $today = Carbon::today()->toDateString();

        $data = $this->model->with('order', 'order.details', 'order.details.product')
        ->whereDate("start_date", '<=', $today)
        ->whereDate('end_date', ">=", $today)
        ->paginate($paginateSize);

        return $data;
    }
}
