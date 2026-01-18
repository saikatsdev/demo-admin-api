<?php

namespace App\Models\BlogPost;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogPost extends BaseModel
{
    public $uploadPath = "uploads/blogPosts";

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogPostCategory::class, 'blog_post_category_id', 'id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, "blog_post_tags", "blog_post_id", "tag_id")->withTimestamps();
    }
}
