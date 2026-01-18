<?php

namespace App\Http\Controllers\Backend\Order;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use App\Repositories\Order\OrderNoteRepository;
use App\Http\Requests\Backend\Order\OrderNoteRequest;

class OrderNoteController extends BaseController
{
    public function __construct(protected OrderNoteRepository $repository) {}

    public function orderNoteList(Request $request)
    {
        if (!$request->user()->hasPermission("orders-update")) {
            return $this->sendError(__("Common.unauthorized"));
        }

        try {
            $orderNotes = $this->repository->orderNoteList($request);

            return $this->sendResponse($orderNotes, "Order note list");
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderNoteStore(OrderNoteRequest $request)
    {
        try {
            $orderNote = $this->repository->orderNoteStore($request);

            return $this->sendResponse($orderNote, "Order note created", 201);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderNoteUpdate(OrderNoteRequest $request, $id)
    {
        try {
            $orderNote = $this->repository->orderNoteUpdate($request, $id);

            return $this->sendResponse($orderNote, "Order note updated successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function orderNoteDelete(Request $request, $id)
    {
        if (!$request->user()->hasPermission("orders-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $orderNote = $this->repository->orderNoteDelete($id);

            return $this->sendResponse($orderNote, "Order Note Deleted successfully", 200);
        } catch (CustomException $exception) {

            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
