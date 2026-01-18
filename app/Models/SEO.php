<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SEO extends BaseModel
{
    public $uploadPath = "uploads/seo";
    
    protected $casts = [
        'meta_keywords' => 'array',
    ];
}