<?php

namespace App\Repositories\BlogPost;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use App\Models\BlogPost\Tag;
use App\Exceptions\CustomException;

class TagRepository
{
    public function __construct(protected Tag $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);

        $tags = $this->model->with(["createdBy:id,username"])
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%")
                ->orWhere("status", $searchKey);
        })
        ->orderBy('created_at', 'desc')
        ->paginate($paginateSize);

        return $tags;
    }

    public function list()
    {
        $tags = $this->model
        ->select("id", "name")
        ->where("status", StatusEnum::ACTIVE)
        ->orderBy("name", "ASC")
        ->get();

        return $tags;
    }

    public function store($request)
    {
        $tag = new $this->model();

        $tag->name   = $request->name;
        $tag->slug   = Str::slug($request->name);
        $tag->status = $request->status;
        $tag->save();

        return $tag;
    }

    public function show($id)
    {
        $tag = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$tag) {
            throw new CustomException("Tag not found");
        }

        return $tag;
    }

    public function update($request, $id)
    {
        $tag = $this->model->find($id);

        if (!$tag) {
            throw new CustomException("Tag Not found");
        }

        $tag->name   = $request->name;
        $tag->slug   = Str::slug($request->name);
        $tag->status = $request->status;
        $tag->save();

        return $tag;
    }

    public function delete($id)
    {
        $tag = $this->model->find($id);
        if (!$tag) {
            throw new CustomException("Tag not found");
        }

        return $tag->delete();
    }
}
