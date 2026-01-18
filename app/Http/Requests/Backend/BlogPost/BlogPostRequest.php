<?php

namespace App\Http\Requests\Backend\BlogPost;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BlogPostRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->id;

        return [
            "title"       => ["required", "unique:blog_posts,title,{$id}"],
            "category_id" => ["nullable", "string", Rule::exists('blog_post_categories', 'id')],
            "image"       => ["sometimes", "nullable", "mimes:jpeg,png,jpg,gif,webp,svg"],
            "description" => ["required"],
            'status'      => ["required", Rule::in(StatusEnum::activeStatus())]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
