<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Backend\Product\GalleryCollection;
use App\Repositories\Product\GalleryRepository;
use Illuminate\Http\Request;

class GalleryController extends BaseController
{
    public function __construct(protected GalleryRepository $repository){}

    public function index(Request $request)
    {
        $images = $this->repository->index($request);

        $images = new GalleryCollection($images);

        return $this->sendResponse($images, "All Images List", 200);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'items'   => 'required|array',
            'items.*' => 'integer|exists:galleries,id',
        ]);

        $deleted = $this->repository->destroy($request->items);

        if ($deleted) {
            return $this->sendResponse([], 'Images deleted successfully', 200);
        }

        return $this->sendError('Failed to delete images', 500);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'items'   => 'required|array',
            'items.*' => 'integer'
        ]);

        $this->repository->restore($request->items);

        return $this->sendResponse([], 'Images restored successfully', 200);
    }

    public function forceDelete(Request $request)
    {
        $request->validate([
            'items'   => 'required|array',
            'items.*' => 'integer'
        ]);

        $this->repository->forceDelete($request->items);

        return $this->sendResponse([], 'Images permanently deleted');
    }
}
