<?php

namespace App\Repositories\CMS;

use App\Helpers\Helper;
use App\Models\CMS\About;
use App\Exceptions\CustomException;

class AboutRepository
{
    public function __construct(protected About $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);

        $abouts = $this->model
        ->with(["createdBy:id,username"])
        ->when($searchKey, fn($query) => $query->where("title", "like", "%$searchKey%"))
        ->orderBy('created_at', 'asc')
        ->paginate($paginateSize);

        return $abouts;
    }

    public function store($request)
    {
        $about = new $this->model();

        $about->title       = $request->title;
        $about->description = $request->description;

        // Upload image
        if ($request->image) {
            $about->img_path = Helper::uploadFile($request->image, $about->uploadPath, $request->height, $request->width);
        }

        $about->save();

        return $about;
    }

    public function show($id)
    {
        $about = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$about) {
            throw new CustomException("About not found");
        }

        return $about;
    }

    public function update($request, $id)
    {
        $about = $this->model->find($id);
        if (!$about) {
            throw new CustomException("About not found");
        }

        $about->title       = $request->title;
        $about->description = $request->description;

        // Upload image one
        if ($request->image) {
            $about->img_path = Helper::uploadFile($request->image, $about->uploadPath, $request->height, $request->width);
        }

        $about->save();

        return $about;
    }

    public function delete($id)
    {
        $about = $this->model->find($id);
        if (!$about) {
            throw new CustomException("About not found");
        }

        //  Delete old image one
        if ($about->img_path) {
            Helper::deleteFile($about->img_path);
        }

        return $about->forceDelete();
    }
}
