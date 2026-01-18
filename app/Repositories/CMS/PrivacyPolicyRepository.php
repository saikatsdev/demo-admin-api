<?php

namespace App\Repositories\CMS;

use Exception;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\CMS\PrivacyPolicy;
use App\Exceptions\CustomException;

class PrivacyPolicyRepository
{
    public function __construct(protected PrivacyPolicy $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $title        = $request->input('title', null);
        $status       = $request->input('status', null);

        $privacies = $this->model->with(["createdBy:id,username"])
        ->when($title, fn($query) => $query->where("title", "like", "%$title%"))
        ->when($status, fn($query) => $query->where("status", $status))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $privacies;
    }

    public function store($request)
    {
        $privacy = new $this->model();

        $privacy->title       = $request->title;
        $privacy->description = $request->description;
        $privacy->status      = $request->status ?? StatusEnum::ACTIVE;
        $privacy->save();

        return $privacy;
    }

    public function show($id)
    {
        $privacy = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$privacy) {
            throw new CustomException("Privacy Policy not found");
        }

        return $privacy;
    }

    public function update($request, $id)
    {
        $privacy = $this->model->find($id);
        if (!$privacy) {
            throw new CustomException("Privacy Policy not found");
        }

        $privacy->title       = $request->title;
        $privacy->description = $request->description;
        $privacy->status      = $request->status;
        $privacy->save();

        return $privacy;
    }

    public function delete($id)
    {
        $privacy = $this->model->find($id);
        if (!$privacy) {
            throw new CustomException("Privacy Policy not found");
        }

        return $privacy->forceDelete();
    }
}
