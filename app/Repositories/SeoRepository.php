<?php

namespace App\Repositories;

use Exception;
use App\Models\SEO;
use App\Helpers\Helper;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;

class SeoRepository
{
    public function __construct(protected SEO $model){}

    public function index($request)
    {
        $seos = $this->model->orderBy('created_at', 'desc')->get();

        return $seos;
    }

    public function store($request)
    {
        try {
            $keywords = array_map('trim', explode(',', $request->meta_keywords));

            $seo = new $this->model();
            $seo->page             = $request->page;
            $seo->meta_title       = $request->meta_title;
            $seo->meta_description = $request->meta_description;
            $seo->meta_keywords    = $keywords;
            $seo->status           = $request->status;
            $seo->width            = $request->width ?? 1260;
            $seo->height           = $request->height ?? 960;

            if ($request->img_path) {
                $image = $request->img_path;

                $imagePath = Helper::uploadFile($image, $seo->uploadPath, $request->height,$request->width);

                $seo->img_path = $imagePath;
            }

            $seo->save();

            return $seo;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new Exception("Failed to create SEO.");
        }
    }

    public function show($id)
    {
        $seo = $this->model->find($id);

        if (!$seo) {
            throw new CustomException("SEO not found.");
        }

        return $seo;
    }

    public function update($request, $id)
    {
        try {
            $seo = $this->model->find($id);

            if (!$seo) {
                throw new CustomException("SEO not found.");
            }

            $seo->page             = $request->page;
            $seo->meta_title       = $request->meta_title;
            $seo->meta_description = $request->meta_description;
            $seo->status           = $request->status;
            $seo->meta_keywords    = array_map('trim', explode(',', $request->meta_keywords));
            $seo->width            = $request->width ?? 1260;
            $seo->height           = $request->height ?? 960;

            if ($request->img_path) {
                $image = $request->img_path;

                $imagePath = Helper::uploadFile($image, $seo->uploadPath, $request->height,$request->width);

                $seo->img_path = $imagePath;
            }

            $seo->save();

            return $seo;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new Exception("Failed to update SEO.");
        }
    }

    public function destroy($id)
    {
        try {
            $seo = $this->model->find($id);

            if (!$seo) {
                throw new CustomException("SEO not found.");
            }

            $seo->delete();

            return $seo;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new Exception("Failed to delete SEO.");
        }
    }
}
