<?php

namespace App\Repositories\CMS;

use App\Models\CMS\Vision;
use App\Exceptions\CustomException;

class VisionRepository
{
    public function __construct(protected Vision $model){}

    public function index($request)
    {
        $visions = $this->model->all();

        return $visions;
    }

    public function store($request)
    {
        $vision = new $this->model;

        $vision->title       = $request->title;
        $vision->description = $request->description;

        $vision->save();

        return $vision;
    }

    public function show($id)
    {
        $vision = $this->model->find($id);

        if(!$vision){
            throw new CustomException("This is not found");
        }

        return $vision;
    }

    public function update($request, $id)
    {
        $vision = $this->model->find($id);

        if(!$vision){
            throw new CustomException("This is not found");
        }

        $vision->title       = $request->title;
        $vision->description = $request->description;

        $vision->save();

        return $vision;
    }

    public function delete($id)
    {
        $vision = $this->model->find($id);

        if(!$vision){
            throw new CustomException("This is not found");
        }

        $vision->delete();

        return true;
    }
}
