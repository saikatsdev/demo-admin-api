<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\File;

class SettingRepository
{
    protected $path = 'modules_statuses.json';

    public function __construct(protected Setting $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $key          = $request->input('key', null);
        $categoryId   = $request->input('setting_category_id', null);
        
        $paginateSize = 100;

        $settings = $this->model->with(["settingCategory:id,name", "createdBy:id,username"])
            ->when($key, fn($query) => $query->where('key', $key))
            ->when($categoryId, fn($query) => $query->where('setting_category_id', $categoryId))
            ->paginate($paginateSize);

        return $settings;
    }

    public function list($request)
    {
        $settings = $this->model->select('id', 'key', 'value', 'type', 'setting_category_id')
            ->when($request->key, fn($query) => $query->where('key', $request->key))
            ->when($request->setting_category_id, fn($query) => $query->where('setting_category_id', $request->setting_category_id))
            ->get();

        return $settings;
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            foreach ($request->items as $key => $item) {
                $setting = $this->model->firstOrNew(['key' => $item["key"]]);

                if ($item["type"] === "image" && $item["value"] instanceof \Illuminate\Http\UploadedFile) {
                    $height = @$item["height"] ?? 200;
                    $width  = @$item["width"] ?? 200;

                    $setting->value = Helper::uploadImage($item["value"], $setting->uploadPath, $height, $width, $setting->value, $key);

                    $setting->height = $height;
                    $setting->width  = $width;

                } elseif ($item["type"] === "image") {
                    $filePath       = parse_url($item["value"], PHP_URL_PATH);
                    $filePath       = ltrim($filePath, '/');
                    $setting->value = $filePath;

                    $setting->height = $item["height"] ?? $setting->height;
                    $setting->width  = $item["width"] ?? $setting->width;
                } else {
                    $setting->value = $item["value"];
                }

                $setting->type                = $item["type"];
                $setting->instruction         = @$item["instruction"];
                $setting->setting_category_id = $item["setting_category_id"];

                $setting->save();
            }

            return true;
        });
    }

    public function show($id)
    {
        $setting = $this->model->with(["settingCategory:id,name", "createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$setting) {
            throw new CustomException('Setting Not found');
        }

        return $setting;
    }

    public function delete($id)
    {
        $setting = $this->model->find($id);

        if (!$setting) {
            throw new CustomException("Setting not found");
        }

        //  Delete old image
        if ($setting->value) {
            Helper::deleteFile($setting->value);
        }

        return $setting->forceDelete();
    }

    public function getModuleStatus()
    {
        return json_decode(File::get(base_path($this->path)), true);
    }

    public function isModuleActive($request)
    {
        $module = $request->input('module');

        if (!$module) {
            return false;
        }

        return Helper::isModuleActive($module);
    }

    public function updateModuleStatus($request)
    {
        $modules = $request->all();

        foreach ($modules as $key => $value) {
            if (!is_bool($value)) {
                throw new CustomException("Invalid value for module '{$key}'. Must be true or false.");
            }
        }

        $path = base_path($this->path);

        File::put($path, json_encode($modules, JSON_PRETTY_PRINT));

        return $modules;
    }
}
