<?php

namespace App\Repositories\Order;

use Exception;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Order\CancelReason;
use App\Exceptions\CustomException;

class CancelReasonRepository
{
    public function __construct(protected CancelReason $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        try {
            $cancelReasons = $this->model
            ->withCount(["orders"])
            ->withSum("orders as total_amount", "payable_price")
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("name", "like", "%$searchKey%");
            })
            ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $cancelReasons;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function list()
    {
        try {
            return $this->model
            ->select("id", "name")
            ->where("status", StatusEnum::ACTIVE)
            ->orderBy("name", "ASC")
            ->get();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            $cancelReason = new $this->model();

            $cancelReason->name   = $request->name;
            $cancelReason->slug   = $request->name;
            $cancelReason->status = $request->status;
            $cancelReason->save();

            return $cancelReason;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $cancelReason = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$cancelReason) {
                throw new CustomException("Cancel reason type not found");
            }

            return $cancelReason;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            $cancelReason = $this->model->find($id);
            if (!$cancelReason) {
                throw new CustomException("Cancel reason not found");
            }

            $cancelReason->name   = $request->name;
            $cancelReason->slug   = $request->name;
            $cancelReason->status = $request->status ?? StatusEnum::ACTIVE;
            $cancelReason->save();

            return $cancelReason;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $cancelReason = $this->model->find($id);
            if (!$cancelReason) {
                throw new CustomException("Cancel reason not found");
            }

            return $cancelReason->delete();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
