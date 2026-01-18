<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class CategorySectionItem extends Model
{
    protected $guarded = [""];

    protected $uploadPath = 'uploads/categories/sections';

    public static  function getUploadPath()
    {
        return (new self)->uploadPath;
    }
}
