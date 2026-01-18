<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    protected $guarded = ["id"];

    protected $uploadPath = 'uploads/products/reviews';

    public static  function getUploadPath()
    {
        return (new self)->uploadPath;
    }
}
