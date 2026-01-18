<?php

namespace App\Models\BlogPost;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPostCategory extends BaseModel
{
    public $uploadPath = "uploads/BlogPostCategories";

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'blog_post_category_id', 'id');
    }
}
