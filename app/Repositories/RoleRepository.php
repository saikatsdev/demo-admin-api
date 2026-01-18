<?php

namespace App\Repositories;

use App\Models\Role;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class RoleRepository
{
    public function __construct(protected Role $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input('search_key', null);

        $roles = $this->model->with(["createdBy:id,username"])
        ->when($searchKey, fn ($query) => $query->where('display_name', 'like', "%$searchKey%"))
        ->orderBy('display_name', 'asc')
        ->paginate($paginateSize);

        return $roles;
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $role = new $this->model();

            $role->display_name = $request->display_name;
            $role->name         = Str::slug($request->display_name, '-');
            $role->description  = $request->description;
            $role->save();

            if ($request->filled('permission_ids')) {
                $role->syncPermissions($request->permission_ids);
            }

            return $role;
        });
    }

    public function show($id)
    {
        $role = $this->model->with('permissions:id,name,display_name')->find($id);

        if (!$role) {
            throw new CustomException('Role not found');
        }

        return $role;
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $role = $this->model->find($id);

            if (!$role) {
                throw new CustomException('Role not found');
            }

            $role->display_name = $request->display_name;
            $role->name         = Str::slug($request->display_name);
            $role->description  = $request->description;
            $role->save();

            if (count($request->permission_ids) > 0) {
                $role->syncPermissions($request->permission_ids);
            }

            return $role;
        });
    }


    public function delete($id)
    {
        $role = $this->model->find($id);
        if (!$role) {
            throw new CustomException('Role not found');
        }

        return $role->forceDelete();
    }
}
