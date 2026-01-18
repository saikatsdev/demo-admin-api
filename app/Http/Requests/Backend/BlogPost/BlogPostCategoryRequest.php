<?php

namespace App\Http\Requests\Backend\BlogPost;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BlogPostCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        $id =  $this->id;

        return [
            'name'   => ['required', "unique:blog_post_categories,name,$id"],
            'image'  => ['nullable', 'mimes:jpeg,png,jpg,gif,webp,svg'],
            'status' => ['required', Rule::in(StatusEnum::activeStatus())],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
