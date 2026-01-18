<?php

namespace App\Repositories\CMS;

use App\Models\CMS\WarrantyPolicy;
use App\Exceptions\CustomException;

class WarrantyPolicyRepository
{
    public function __construct(protected WarrantyPolicy $model){}

    public function index($request)
    {
        $policies = $this->model->all();

        return $policies;
    }

    public function store($request)
    {
        $policy = new $this->model;

        $policy->title       = $request->title;
        $policy->description = $request->description;

        $policy->save();

        return $policy;
    }

    public function show($id)
    {
        $policy = $this->model->find($id);

        if(!$policy){
            throw new CustomException("This is not found");
        }

        return $policy;
    }

    public function update($request, $id)
    {
        $policy = $this->model->find($id);

        if(!$policy){
            throw new CustomException("This is not found");
        }

        $policy->title       = $request->title;
        $policy->description = $request->description;

        $policy->save();

        return $policy;
    }

    public function delete($id)
    {
        $policy = $this->model->find($id);

        if(!$policy){
            throw new CustomException("This is not found");
        }

        $policy->delete();

        return true;
    }
}
