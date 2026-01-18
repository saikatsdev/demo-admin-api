<?php

namespace App\Repositories\Product;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Product\Product;
use App\Models\Product\Section;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class SectionRepository
{
    public function __construct(protected Section $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);

        $sections = $this->model->with([
            "products" => fn($query) => $query->where("status", StatusEnum::ACTIVE)->orderBy("discount", "desc"),
            "products.downSells" => fn($q) => $q->valid(),
            "products.category:id,name",
            "products.brand:id,name",
            "products.images",
            "products.productReviews",
            "products.productReviews.reply:id,product_review_id,reply,created_at",
            "products.variations",
            "products.variations.attributeValue1:id,value,attribute_id",
            "products.variations.attributeValue2:id,value,attribute_id",
            "products.variations.attributeValue3:id,value,attribute_id",
            "createdBy:id,username",
            "banners"
        ])
        ->when($searchKey, fn($query) => $query->where("title", "like", "%$searchKey%"))
        ->when($status, fn($query) => $query->where("status", $status))
        ->orderBy("position", "ASC")
        ->paginate($paginateSize);

        return $sections;
    }
    
    public function list($request)
    {
        $sections = $this->model->select("id", "title", "position")->where("banner_status", StatusEnum::ACTIVE)->get();

        return $sections;
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $section = new $this->model();

            $section->title         = $request->title;
            $section->status        = $request->status;
            $section->position      = $request->position;
            $section->is_slider     = $request->is_slider ?? false;
            $section->link          = $request->link;
            $section->banner_status = $request->banner_status ?? "active";

            if ($request->banner_image) {
                $section->img_path = Helper::uploadFile($request->banner_image, $section->uploadPath, $request->height, $request->width);
            }

            $section->save();

            // Determine product IDs
            $productIds = $request->category_id ? Product::where('category_id', $request->category_id)->pluck('id') : $request->product_ids;

            $pivotData = collect($productIds)->mapWithKeys(fn($id) => [$id => ['created_at' => now()]])->toArray();

            $section->products()->sync($pivotData);

            return $section;
        });
    }

    public function show($id)
    {
        $section = $this->model->with([
            "products",
            "products.category:id,name",
            "products.brand:id,name",
            "products.variations",
            "products.variations.attributeValue1:id,value",
            "products.variations.attributeValue2:id,value",
            "products.variations.attributeValue3:id,value",
            "createdBy:id,username",
            "updatedBy:id,username",
            "banners"
        ])->find($id);

        if (!$section) {
            throw new CustomException("Section not found");
        }

        return $section;
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $section = $this->model->find($id);

            if (!$section) {
                throw new CustomException("Section Not found");
            }

            $section->title     = $request->title;
            $section->status    = $request->status;
            $section->position  = $request->position;
            $section->is_slider = $request->is_slider ?? false;
            $section->link      = $request->link;
            $section->save();

            $productIds = $request->category_id ? Product::where("category_id", $request->category_id)->pluck("id") : $request->product_ids;

            $pivotData = collect($productIds)->mapWithKeys(fn($id) => [$id => ['created_at' => now()]])->toArray();

            $section->products()->sync($pivotData);

            return $section;
        });
    }

    public function delete($id)
    {
        $section = $this->model->find($id);

        if (!$section) {
            throw new CustomException("Section not found");
        }

        $section->products()->detach();

        return $section->forceDelete();
    }
}
