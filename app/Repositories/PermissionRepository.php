<?php

namespace App\Repositories;

use Exception;
use App\Helpers\Helper;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Exceptions\CustomException;

class PermissionRepository
{
    public function __construct(protected Permission $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $displayName  = $request->input('display_name', null);

        $permissions = $this->model->with(["createdBy:id,username"])
        ->when($displayName, fn ($query) => $query->where('display_name', 'like', "%$displayName%"))
        ->orderBy('display_name', 'asc')
        ->paginate($paginateSize);

        return $permissions;
    }

    public function list()
    {
        $permissions = $this->model
            ->orderBy('module_name')
            ->orderBy('group')
            ->orderBy('id')
            ->get()
            ->groupBy('module_name')
            ->map(function ($modulePermissions) {
                return $modulePermissions->groupBy('group')->map(function ($groupPermissions) {
                    return $groupPermissions->map(function ($permission) {
                        $words = explode(' ', $permission->display_name);
                        $lastWord = ucfirst(strtolower(end($words)));

                        return [
                            'id' => $permission->id,
                            'display_name' => $lastWord,
                        ];
                    })->sortBy('id')->values();
                });
            });

        return $permissions;
    }



    public function store($request)
    {
        $permission = new $this->model();

        $permission->display_name = $request->display_name;
        $permission->name         = Str::slug($request->display_name, '-');
        $permission->group        = $request->group;
        $permission->description  = $request->description;
        $permission->save();

        return $permission;
    }

    public function show($id)
    {
        $permission = $this->model->with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

        if (!$permission) {
            throw new CustomException('Permission not found');
        }

        return $permission;
    }

    public function update($request, $id)
    {
        $permission = $this->model->find($id);

        if (!$permission) {
            throw new CustomException('Permission not found');
        }

        $permission->display_name = $request->display_name;
        $permission->name         = Str::slug($request->display_name, '-');
        $permission->group        = $request->group;
        $permission->description  = $request->description;
        $permission->save();

        return $permission;
    }

    public function delete($id)
    {
        $permission = $this->model->find($id);
        if (!$permission) {
            throw new CustomException('Permission not found');
        }

        return $permission->forceDelete();
    }
}
