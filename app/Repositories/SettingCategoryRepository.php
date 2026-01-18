<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use App\Models\SettingCategory;
use App\Exceptions\CustomException;

class SettingCategoryRepository
{
    public function __construct(protected SettingCategory $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $name         = $request->input("name", null);
        $status       = $request->input("status", null);

        $settingCategories = $this->model->with(["createdBy:id,username"])
        ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
        ->when($status, fn($query) => $query->where("status", $status))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $settingCategories;
    }

    public function store($request)
    {
        $settingCategory = new $this->model();

        $settingCategory->name   = $request->name;
        $settingCategory->slug   = Str::slug($request->name);
        $settingCategory->status = $request->status ?? StatusEnum::ACTIVE;
        $settingCategory->save();

        return $settingCategory;
    }

    public function show($id)
    {
        $settingCategory = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$settingCategory) {
            throw new CustomException("Setting category not found");
        }

        return $settingCategory;
    }

    public function update($request, $id)
    {
        $SettingCategory = $this->model->find($id);

        if (!$SettingCategory) {
            throw new CustomException("Setting category not found");
        }

        $SettingCategory->name   = $request->name;
        $SettingCategory->slug   = Str::slug($request->name);
        $SettingCategory->status = $request->status;
        $SettingCategory->save();

        return $SettingCategory;
    }

    public function delete($id)
    {
        $settingCategory = $this->model->find($id);
        if (!$settingCategory) {
            throw new CustomException("Setting category not found");
        }

        return $settingCategory->forceDelete();
    }
}
