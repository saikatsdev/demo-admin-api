<?php

namespace App\Repositories\CMS;

use App\Models\CMS\Mission;
use App\Exceptions\CustomException;

class MissionRepository
{
    public function __construct(protected Mission $model){}

    public function index($request)
    {
        $missions = $this->model->all();

        return $missions;
    }

    public function store($request)
    {
        $mission = new $this->model;

        $mission->title       = $request->title;
        $mission->description = $request->description;

        $mission->save();

        return $mission;
    }

    public function show($id)
    {
        $mission = $this->model->find($id);

        if(!$mission){
            throw new CustomException("This is not found");
        }

        return $mission;
    }

    public function update($request, $id)
    {
        $mission = $this->model->find($id);

        if(!$mission){
            throw new CustomException("This is not found");
        }

        $mission->title       = $request->title;
        $mission->description = $request->description;

        $mission->save();

        return $mission;
    }

    public function delete($id)
    {
        $mission = $this->model->find($id);

        if(!$mission){
            throw new CustomException("This is not found");
        }

        $mission->delete();

        return true;
    }
}
