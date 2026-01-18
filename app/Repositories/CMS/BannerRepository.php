<?php

namespace App\Repositories\CMS;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\CMS\Banner;
use App\Exceptions\CustomException;

class BannerRepository
{
    public function __construct(protected Banner $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $title        = $request->input('title', null);
        $status       = $request->input("status", null);

        $banners = $this->model
        ->with(["createdBy:id,username"])
        ->when($title, fn($query) => $query->where("title", "like", "%$title%"))
        ->when($status, fn($query) => $query->where("status", $status))
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $banners;
    }

    public function store($request)
    {
        $banner = new $this->model();

        $banner->title       = $request->title;
        $banner->section_id  = $request->section_id;
        $banner->device_type = $request->device_type;
        $banner->type        = $request->type;
        $banner->link        = $request->link;
        $banner->status      = $request->status;
        $banner->description = $request->description;

        // Upload image
        if ($request->image) {
            $banner->img_path = Helper::uploadFile($request->image, $banner->uploadPath, $request->height, $request->width);
            $banner->width    = $request->width;
            $banner->height   = $request->height;
        }

        $banner->save();

        return $banner;
    }

    public function show($id, $status = null)
    {
        $banner = $this->model
        ->with(["createdBy:id,username", "updatedBy:id,username"])
        ->when($status, fn($query) => $query->where("status", $status))
        ->find($id);

        if (!$banner) {
            throw new CustomException("Banner not found");
        }

        return $banner;
    }

    public function update($request, $id)
    {
        $banner = $this->model->find($id);
        if (!$banner) {
            throw new CustomException("Banner Not found");
        }

        $banner->title       = $request->title;
        $banner->section_id  = $request->section_id;
        $banner->device_type = $request->device_type;
        $banner->type        = $request->type;
        $banner->link        = $request->link;
        $banner->status      = $request->status ?? StatusEnum::ACTIVE;
        $banner->description = $request->description;

        // Update image
        if ($request->image) {
            $banner->img_path = Helper::uploadFile($request->image, $banner->uploadPath, $request->height, $request->width);
            $banner->width    = $request->width;
            $banner->height   = $request->height;
        }

        $banner->save();

        return $banner;
    }

    public function delete($id)
    {
        $banner = $this->model->find($id);
        if (!$banner) {
            throw new CustomException("Banner not found");
        }

        return $banner->forceDelete();
    }
}
