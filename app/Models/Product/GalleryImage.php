<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $guarded = ["id"];

    protected $uploadPath = 'uploads/products/gallery';

    public static  function getUploadPath()
    {
        return (new self)->uploadPath;
    }
}
