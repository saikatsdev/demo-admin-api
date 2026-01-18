<?php

namespace App\Repositories\CMS;

use App\Helpers\Helper;
use App\Models\CMS\ReturnPolicy;
use App\Exceptions\CustomException;

class ReturnPolicyRepository
{
    public function __construct(protected ReturnPolicy $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $title        = $request->input('title', null);
        $status       = $request->input('status', null);

        $returnPolicies = $this->model->with(["createdBy:id,username"])
        ->when($title, fn($query) => $query->where("title", "like", "%$title%"))
        ->when($status, fn($query) => $query->where("status", $status))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $returnPolicies;
    }

    public function store($request)
    {
        $returnPolicy = new $this->model();

        $returnPolicy->title       = $request->title;
        $returnPolicy->description = $request->description;
        $returnPolicy->save();

        return $returnPolicy;
    }

    public function show($id)
    {
        $returnPolicy = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$returnPolicy) {
            throw new CustomException("Return Policy not found");
        }

        return $returnPolicy;
    }

    public function update($request, $id)
    {
        $returnPolicy = $this->model->find($id);
        if (!$returnPolicy) {
            throw new CustomException("Return Policy not found");
        }

        $returnPolicy->title       = $request->title;
        $returnPolicy->description = $request->description;
        $returnPolicy->save();

        return $returnPolicy;
    }

    public function delete($id)
    {
        $returnPolicy = $this->model->find($id);
        if (!$returnPolicy) {
            throw new CustomException("Return Policy not found");
        }

        return $returnPolicy->forceDelete();
    }
}
