<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\UserCategory;
use App\Exceptions\CustomException;

class UserCategoryRepository
{
    public function __construct(protected UserCategory $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        $userCategories = $this->model->with(["createdBy:id,username"])
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", $searchKey);
        })
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $userCategories;
    }

    public function store($request)
    {
        $userCategory = new $this->model();

        $userCategory->name   = $request->name;
        $userCategory->slug   = $request->name;
        $userCategory->status = $request->status ?? StatusEnum::ACTIVE;
        $userCategory->save();

        return $userCategory;
    }

    public function show($id)
    {
        $userCategory = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$userCategory) {
            throw new CustomException("User category not found");
        }

        return $userCategory;
    }

    public function update($request, $id)
    {
        $userCategory = $this->model->find($id);
        if (!$userCategory) {
            throw new CustomException("User category Not found");
        }

        $userCategory->name   = $request->name;
        $userCategory->slug   = $request->name;
        $userCategory->status = $request->status ?? StatusEnum::ACTIVE;
        $userCategory->save();


        return $userCategory;
    }

    public function delete($id)
    {
        $userCategory = $this->model->find($id);

        if (!$userCategory) {
            throw new CustomException("User category not found");
        }

        return $userCategory->forceDelete();
    }
}
