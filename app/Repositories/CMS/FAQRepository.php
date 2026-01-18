<?php

namespace App\Repositories\CMS;

use Exception;
use App\Helpers\Helper;
use App\Models\CMS\FAQ;
use App\Exceptions\CustomException;

class FAQRepository
{
    public function __construct(protected FAQ $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $status       = $request->input('status', null);

        $faqs = $this->model->with(["createdBy:id,username"])
        ->when($status, fn($query) => $query->where("status", $status))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $faqs;
    }

    public function store($request)
    {
        $faq = new $this->model();

        $faq->question = $request->question;
        $faq->answer   = $request->answer;
        $faq->status   = $request->status;
        $faq->save();

        return $faq;
    }

    public function show($id)
    {
        $faq = $this->model
        ->with(["createdBy:id,username", "updatedBy:id,username"])
        ->find($id);

        if (!$faq) {
            throw new CustomException("FAQ not found");
        }

        return $faq;
    }

    public function update($request, $id)
    {
        $faq = $this->model->find($id);
        if (!$faq) {
            throw new CustomException("FAQ not found");
        }

        $faq->question = $request->question;
        $faq->answer   = $request->answer;
        $faq->status   = $request->status;
        $faq->save();

        return $faq;
    }

    public function delete($id)
    {
        $faq = $this->model->find($id);
        if (!$faq) {
            throw new CustomException("FAQ not found");
        }

        return $faq->forceDelete();
    }
}
