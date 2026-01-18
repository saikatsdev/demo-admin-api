<?php

namespace App\Repositories\Product;

use App\Exceptions\CustomException;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use App\Models\Product\ProductReview;

class ProductReviewRepository
{
    public function __construct(protected ProductReview $model){}

    public function index($request)
    {
        $reviews = $this->model->with("product:id,name,slug,img_path", "reply")->orderBy('created_at','desc')->get();

        return $reviews;
    }

    public function store($request)
    {
        $review = new $this->model;

        $review->product_id = $request->product_id;
        $review->name       = Str::title($request->name);
        $review->email      = $request->email ?? NULL;
        $review->title      = $request->title ?? NULL;
        $review->rating     = $request->rating ?? NULL;
        $review->review     = $request->review ?? NULL;

        if($request->hasFile('image')){
            $review->image = Helper::uploadImage($request->image,$review->uploadPath, $request->height, $request->width);
        }

        $review->save();

        return $review;
    }

    public function show($id)
    {
        $review = $this->model->find($id);

        if(!$review){
            throw new CustomException("Review Not Found");
        }

        return $review;
    }

    public function update($request, $id)
    {
        $review = $this->model->find($id);

        if(!$review){
            throw new CustomException("Review not found");
        }

        $review->name   = Str::title($request->name);
        $review->email  = $request->email ?? NULL;
        $review->title  = $request->title ?? NULL;
        $review->rating = $request->rating ?? NULL;
        $review->review = $request->review ?? NULL;

        if($request->hasFile('image')){
            $review->image = Helper::uploadImage($request->image,$review->uploadPath, $request->height, $request->width);
        }

        $review->save();

        return $review;
    }

    public function statusUpdate($request)
    {
        $review = $this->model->find($request->id);

        if(!$review){
            throw new CustomException("Review not found");
        }

        $review->status = $request->status;

        $review->save();

        return $review;
    }

    public function destroy($id)
    {
        $review = $this->model->find($id);

        if(!$review){
            throw new CustomException("Review not found");
        }

        $review->delete();

        return true;
    }
}
