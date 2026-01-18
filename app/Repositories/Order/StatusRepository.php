<?php

namespace App\Repositories\Order;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\Status;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;

class StatusRepository
{
    public function __construct(protected Status $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $status       = $request->input('status', null);
        $searchKey    = $request->input('search_key', null);

        $authUserId = Auth::id();
        $isAdmin    = Helper::isAdminOrTeamLead();

        // start from a fresh query builder
        $query = $this->model->newQuery();

        // Admin vs Non-admin (only one branch will apply)
        $query->when(!$isAdmin, function ($q) use ($authUserId) {

            // Orders Count + Sum (user filtered)
            $q->withCount(['orders' => fn($sq) => $sq->where('assign_user_id', $authUserId)])
                ->withSum(['orders as total_amount' => fn($sq) => $sq->where('assign_user_id', $authUserId)], 'payable_price');

            // Courier Pending Count + Sum (user filtered)
            $q->withCount(['courierPending' => fn($sq) => $sq->where('assign_user_id', $authUserId)])
                ->withSum(['courierPending as courier_pending_amount' => fn($sq) => $sq->where('assign_user_id', $authUserId)], 'payable_price');

            // Courier Received Count + Sum (user filtered)
            $q->withCount(['courierReceived' => fn($sq) => $sq->where('assign_user_id', $authUserId)])
                ->withSum(['courierReceived as courier_received_amount' => fn($sq) => $sq->where('assign_user_id', $authUserId)], 'payable_price');
        }, function ($q) {
            // Admin / Team Lead sees totals across all users
            $q->withCount(['orders', 'courierPending', 'courierReceived'])
                ->withSum('orders as total_amount', 'payable_price')
                ->withSum('courierPending as courier_pending_amount', 'payable_price')
                ->withSum('courierReceived as courier_received_amount', 'payable_price');
        });

        // Filters
        $query->when($searchKey, fn($q) => $q->where('name', 'like', "%{$searchKey}%"));
        $query->when($status,    fn($q) => $q->where('status', $status));

        // Ordering + paginate
        return $query->orderBy('position', 'ASC')->paginate($paginateSize);
    }


    public function list()
    {
        return $this->model
        ->select("id", "name", "bg_color", "text_color")
        ->where("status", StatusEnum::ACTIVE)
        ->orderBy("position", "ASC")
        ->get();
    }

    public function store($request)
    {
        $status = new $this->model();

        $status->name       = $request->name;
        $status->slug       = $request->name;
        $status->bg_color   = $request->bg_color;
        $status->text_color = $request->text_color;
        $status->status     = $request->statuses;
        $status->position   = $request->position;
        $status->save();

        return $status;
    }

    public function show($id)
    {
        $status = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$status) {
            throw new CustomException("Status not found");
        }

        return $status;
    }

    function update($request, $id)
    {
        $status = $this->model->find($id);

        if (!$status) {
            throw new CustomException("Status Not found");
        }

        $status->name       = $request->name;
        $status->slug       = $request->name;
        $status->text_color = $request->text_color;
        $status->bg_color   = $request->bg_color;
        $status->status     = $request->status;
        $status->position   = $request->position;
        $status->save();

        return $status;
    }

    public function delete($id)
    {
        $status = $this->model->find($id);

        if (!$status) {
            throw new CustomException("Status not found");
        }

        return $status->forceDelete();
    }
}
