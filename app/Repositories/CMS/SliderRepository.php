<?php

namespace App\Repositories\CMS;

use App\Helpers\Helper;
use App\Models\CMS\Slider;
use App\Exceptions\CustomException;

class SliderRepository
{
    public function __construct(protected Slider $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);

        $sliders = $this->model
        ->orderBy('created_at', 'desc')
        ->when($searchKey, fn($query) => $query->where("title", "like", "%$searchKey%"))
        ->when($status, fn($query) => $query->where("status", $status))
        ->paginate($paginateSize);

        return $sliders;
    }

    public function store($request)
    {
        $slider = new $this->model();

        $slider->title  = $request->title;
        $slider->type   = $request->type;
        $slider->status = $request->status;

        // Upload image
        if ($request->image) {
            $slider->img_path = Helper::uploadFile($request->image, $slider->uploadPath, $request->height, $request->width);
            $slider->width    = $request->width;
            $slider->height   = $request->height;
        }

        $slider->save();

        return $slider;
    }

    public function show($id)
    {
        $slider = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$slider) {
            throw new CustomException('Slider not found');
        }

        return $slider;
    }

    public function update($request, $id)
    {
        $slider = $this->model->find($id);

        if (!$slider) {
            throw new CustomException("Slider not found");
        }

        $slider->title  = $request->title;
        $slider->type   = $request->type;
        $slider->status = $request->status;

        // Update image
        if ($request->image) {
            $slider->img_path = Helper::uploadFile($request->image, $slider->uploadPath, $request->height, $request->width, $slider->img_path);
            $slider->width    = $request->width;
            $slider->height   = $request->height;
        }

        $slider->save();

        return $slider;
    }

    public function delete($id)
    {
        $slider = $this->model->find($id);
        if (!$slider) {
            throw new CustomException('Slider not found');
        }
        // Delete old image
        if ($slider->img_path) {
            Helper::deleteFile($slider->img_path);
        }

        return $slider->forceDelete();
    }
}
