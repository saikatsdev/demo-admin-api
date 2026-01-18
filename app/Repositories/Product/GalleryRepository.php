<?php

namespace App\Repositories\Product;

use App\Models\Product\Gallery;
use Illuminate\Support\Facades\File;

class GalleryRepository
{
    public function __construct(protected Gallery $model){}

    public function index($request)
    {
        $trash = $request->boolean('trash');

        $query = $this->model::query();

        if ($trash) {
            $query->onlyTrashed();
        }

        $data = $query->select('id', 'img_path')->paginate(20);

        return $data;
    }

    public function destroy(array $items)
    {
        $galleries = $this->model->whereIn('id', $items)->get();

        if ($galleries->isEmpty()) {
            return false;
        }

        foreach ($galleries as $gallery) {
            $gallery->delete();
        }

        return true;
    }

    public function restore(array $items)
    {
        return $this->model->onlyTrashed()->whereIn('id', $items)->restore();
    }

    public function forceDelete(array $items)
    {
        $galleries = $this->model->onlyTrashed()->whereIn('id', $items)->get();

        foreach ($galleries as $gallery) {
            if ($gallery->img_path && File::exists(public_path($gallery->img_path))) {
                File::delete(public_path($gallery->img_path));
            }

            $gallery->forceDelete();
        }

        return true;
    }
}
