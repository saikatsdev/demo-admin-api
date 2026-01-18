<?php

namespace App\Repositories\Product;

use Exception;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Models\Product\CategorySection;
use App\Models\Product\CategorySectionItem;

class CategorySectionRepository
{
    public function __construct(protected CategorySection $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);
        $status       = $request->input("status", null);

        try {
            $categorySections = $this->model->with([
                "categories",
                "createdBy:id,username"
            ])
            ->when($searchKey, fn($query) => $query->where("title", "like", "%$searchKey%"))
            ->when($status, fn($query) => $query->where("status", $status))
            ->orderBy("position", "asc")
            ->paginate($paginateSize);

            return $categorySections;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $categorySection = new $this->model();
            
            $categorySection->title    = $request->title;
            $categorySection->status   = $request->status;
            $categorySection->position = $request->position;
            $categorySection->link     = $request->link;
            $categorySection->save();

            $categorySectionItems = [];
            foreach ($request->items as $key => $item) {
                $categorySectionItems[] = [
                    "category_section_id" => $categorySection->id,
                    "category_id"         => $item["category_id"],
                    "link"                => $item["link"],
                    "img_path"            => Helper::uploadFile(
                        @$item["image"], 
                        CategorySectionItem::getUploadPath(), 
                        isset($item["height"]) && $item["height"] > 0 ? $item["height"] : 450,
                        isset($item["width"])  && $item["width"] > 0 ? $item["width"]  : 450,
                        $key
                    ),
                    "created_at"          => now()
                ];
            }


            $categorySection->categories()->sync($categorySectionItems);

            DB::commit();

            return $categorySection;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $section = $this->model->with([
                "categories",
                "createdBy:id,username",
                "updatedBy:id,username"
            ])->find($id);

            if (!$section) {
                throw new CustomException("Category section not found");
            }

            return $section;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $categorySection = $this->model->find($id);

            if (!$categorySection) {
                throw new CustomException("Category section Not found");
            }

            $categorySection->title    = $request->title;
            $categorySection->status   = $request->status;
            $categorySection->position = $request->position;
            $categorySection->link     = $request->link;
            $categorySection->save();

            $categorySectionItems = [];
            foreach ($request->items as $key => $item) {
                // Delete old section image
                if (!empty($request->delete_image_ids)) {
                    $oldImages = CategorySectionItem::whereIn("id", $request->delete_image_ids)->get();

                    foreach ($oldImages as $oldImage) {
                        Helper::deleteFile(@$oldImage->img_path);
                    }
                }

                if (@$item["image"] instanceof \Illuminate\Http\UploadedFile) {
                    $imgPath = Helper::uploadFile(@$item["image"], CategorySectionItem::getUploadPath(), $request->height, $request->width, $key);
                } else {
                    $categorySectionItem = CategorySectionItem::find($item["id"]);
                    $imgPath = @$categorySectionItem->img_path;
                }

                $categorySectionItems[] = [
                    "category_section_id" => $categorySection->id,
                    "category_id"         => $item["category_id"],
                    "link"                => $item["link"],
                    "img_path"            => $imgPath,
                    "created_at"          => now()
                ];
            }

            // Delete section products
            $categorySection->categories()->detach();
            $categorySection->categories()->sync($categorySectionItems);

            DB::commit();

            return $categorySection;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $categorySection = $this->model->find($id);
            if (!$categorySection) {
                throw new CustomException("Section not found");
            }

            $categorySection->categories()->detach();

            return $categorySection->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
