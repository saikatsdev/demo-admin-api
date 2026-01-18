<?php

namespace App\Repositories\CMS;

use Exception;
use App\Helpers\Helper;
use App\Exceptions\CustomException;
use App\Models\CMS\TermsAndCondition;

class TermsAndConditionRepository
{
    public function __construct(protected TermsAndCondition $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $title        = $request->input('title', null);
        $status       = $request->input('status', null);

        $terms = $this->model->with(["createdBy:id,username"])
        ->when($title, fn($query) => $query->where("title", "like", "%$title%"))
        ->when($status, fn($query) => $query->where("status", $status))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $terms;
    }

    public function store($request)
    {
        $term = new $this->model();

        $term->title       = $request->title;
        $term->description = $request->description;
        $term->status      = $request->status;
        $term->save();

        return $term;
    }

    public function show($id)
    {
        $term = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$term) {
            throw new CustomException("Terms And Condition not found");
        }

        return $term;
    }

    public function update($request, $id)
    {
        $term = $this->model->findOrFail($id);

        $term->title       = $request->title;
        $term->description = $request->description;
        $term->status      = $request->status;
        $term->save();

        return $term;
    }

    public function delete($id)
    {
        $term = $this->model->find($id);
        if (!$term) {
            throw new CustomException('Terms And Condition not found');
        }

        return $term->forceDelete();
    }
}
