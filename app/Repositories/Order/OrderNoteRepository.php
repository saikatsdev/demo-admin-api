<?php

namespace App\Repositories\Order;

use App\Models\Order\OrderNote;
use App\Exceptions\CustomException;

class OrderNoteRepository
{
    public function __construct(protected OrderNote $model) {}

    public function orderNoteList($request)
    {
        $orderId = $request->input('order_id', null);

        $orderNotes = $this->model
        ->with(["createdBy:id,username"])
        ->when($orderId, fn($query) => $query->where("order_id", $orderId))
        ->orderBy("created_at", "desc")
        ->get();

        return $orderNotes;
    }

    public function orderNoteStore($request)
    {
        $orderNote = new $this->model();

        $orderNote->order_id = $request->order_id;
        $orderNote->note     = $request->note;
        $orderNote->save();

        $orderNote->load(["createdBy:id,username"]);

        return $orderNote;
    }

    public function orderNoteUpdate($request, $id)
    {
        $orderNote = $this->model->find($id);

        if (!$orderNote) {
            throw new CustomException("Order note not found");
        }

        $orderNote->note = $request->note;
        $orderNote->save();

        $orderNote->load(["createdBy:id,username", "updatedBy:id,username"]);

        return $orderNote;
    }

    public function orderNoteDelete($id)
    {
        $orderNote = $this->model->find($id);

        if (!$orderNote) {
            throw new CustomException("order note not found");
        }

        return $orderNote->forceDelete();
    }
}
