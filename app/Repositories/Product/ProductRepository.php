<?php

namespace App\Repositories\Product;

use App\Helpers\Helper;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use App\Models\Product\Brand;
use App\Models\Product\Product;
use App\Models\Product\Category;
use App\Models\Product\Attribute;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Models\Product\ReviewImage;
use App\Models\Product\GalleryImage;
use App\Models\Product\ProductVariation;

class ProductRepository
{
    public function __construct(protected Product $model){}

    public function index($request)
    {
        $paginateSize       = Helper::checkPaginateSize($request);
        $searchKey = trim($request->input('search_key', ''));
        $searchKey = preg_replace('/\s+/', ' ', $searchKey);
        $searchTerms = explode(' ', $searchKey);
        $brandIds           = $request->input("brand_ids", []);
        $categoryIds        = $request->input("category_ids", []);
        $subCategoryIds     = $request->input("sub_category_ids", []);
        $subSubCategoryIds  = $request->input("sub_sub_category_ids", []);
        $attributesValueIds = $request->input("attribute_value_ids", []);
        $startDate          = $request->input("start_date", null);
        $endDate            = $request->input("end_date", null);

        if ($startDate && $endDate) {
            $startDate = Helper::startOfDate($startDate);
            $endDate   = Helper::endOfDate($endDate);
        }

        $products = $this->model->select(
            "id", "name", "slug", "brand_id", "category_id", "sub_category_id", "sub_sub_category_id", "product_type_id", "status", "buy_price",
            "mrp", "offer_price", "discount", "sell_price", "offer_percent", "total_purchase_qty", "total_sell_qty", "current_stock",
            "minimum_qty", "alert_qty", "sku", "free_shipping", "img_path", "video_url"
        )->with([
            "category:id,name,slug",
            "brand:id,name,slug",
            "subCategory:id,name,slug",
            "subSubCategory:id,name,slug",
            "images" => fn ($q) => $q->limit(1),
            "downSells" => fn($q) => $q->valid(),
			"productReviews:id,product_id,name,email,image,title,rating,review,created_at,updated_at",
            "productReviews.reply",
            "variations",
            "variations.attributeValue1:id,value,attribute_id",
            "variations.attributeValue2:id,value,attribute_id",
            "variations.attributeValue3:id,value,attribute_id",
            "variations.attributeValue1.attribute:id,name",
            "variations.attributeValue2.attribute:id,name",
            "variations.attributeValue3.attribute:id,name"
        ])
        ->when($searchKey, function($query) use ($searchTerms) {
            $query->where(function($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->whereRaw("LOWER(name) LIKE ?", ["%".strtolower($term)."%"]);
                }
            });
        })
        ->when($request->status, fn ($query) => $query->where("status", $request->status))
        ->when($request->product_type_id, fn ($query) => $query->where("product_type_id", $request->product_type_id))
        ->when(($request->min_price && $request->max_price), fn($query) => $query->whereBetween("sell_price", [$request->min_price, $request->max_price]))
        ->when(($request->min_stock && $request->max_stock), fn($query) => $query->whereBetween("current_stock", [$request->min_stock, $request->max_stock]))
        ->when(($startDate && $endDate), fn($query) => $query->whereBetween("created_at", [$startDate, $endDate]))
        ->when($request->requestFrom, function ($query) {
            $query->where(function ($q) {
                $q->whereNotIn("product_type_id", [4])
                    ->orWhereNull("product_type_id");
            });
        })
        ->when($brandIds, fn ($query) => $query->whereHas("brand", function($q) use ($brandIds) {
            $q->whereIn("id", $brandIds);
        }))
        ->when($categoryIds, fn ($query) => $query->whereHas("category", function($q) use ($categoryIds) {
            $q->whereIn("id", $categoryIds)->orWhereIn("slug", $categoryIds);
        }))
        ->when($subCategoryIds, fn ($query) => $query->whereHas("subCategory", function($q) use ($subCategoryIds) {
            $q->whereIn("id", $subCategoryIds)->orWhereIn("slug", $subCategoryIds);
        }))
        ->when($subSubCategoryIds, fn ($query) => $query->whereHas("subSubCategory", function($q) use ($subSubCategoryIds) {
            $q->whereIn("id", $subSubCategoryIds)->orWhereIn("slug", $subSubCategoryIds);
        }))
        ->when($attributesValueIds, fn ($query) => $query->whereHas("variations", function($q) use ($attributesValueIds) {
            $q->whereIn("attribute_value_id_1", $attributesValueIds)
            ->orWhereIn("attribute_value_id_2", $attributesValueIds)
            ->orWhereIn("attribute_value_id_3", $attributesValueIds);
        }))
        ->when(
            $request->request_from === 'backend',
            fn ($query) => $query->orderBy('created_at', 'DESC'),
            fn ($query) => $query->orderBy('discount', 'DESC')
        )
        ->paginate($paginateSize);

        return $products;
    }

    public function countByStatus($status)
    {
        return $this->model->where('status', $status)->count();
    }

    public function list($request)
    {
        $searchKey = $request->input("search_key", null);

        $products = $this->model->select("id", "name", "slug", "brand_id", "category_id", "mrp", "offer_price", "discount", "sell_price", "offer_percent",
        "current_stock", "minimum_qty", "sku", "free_shipping","img_path"
        )->with([
            "category:id,name,slug",
            "brand:id,name,slug",
            "variations",
            "variations.attributeValue1:id,value,attribute_id",
            "variations.attributeValue2:id,value,attribute_id",
            "variations.attributeValue3:id,value,attribute_id",
            "variations.attributeValue1.attribute:id,name",
            "variations.attributeValue2.attribute:id,name",
            "variations.attributeValue3.attribute:id,name"
        ])
        ->where("status", StatusEnum::ACTIVE)
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%")
            ->orWhere("sku", "like", "%$searchKey%");
        })
        ->orderBy("discount", "DESC")
        ->limit(20)
        ->get()
        ->map(function ($item) {
            return [
                "id"            => $item->id,
                "name"          => $item->name,
                "slug"          => $item->slug,
                "sku"           => $item->sku,
                "mrp"           => $item->mrp,
                "offer_price"   => $item->offer_price,
                "discount"      => $item->discount,
                "sell_price"    => $item->sell_price,
                "offer_percent" => $item->offer_percent,
                "current_stock" => $item->current_stock,
                "minimum_qty"   => $item->minimum_qty,
                "free_shipping" => $item->free_shipping,
                "img_path"      => Helper::getFilePath($item->img_path),
                "image"         => Helper::getFilePath($item->img_path),
                "category"      => $item->category,
                "brand"         => $item->brand,
                "variations"    => $item->variations->map(function ($variation) {
                    return [
                        "current_stock"    => $variation->current_stock,
                        "mrp"              => $variation->mrp,
                        "offer_price"      => $variation->offer_price,
                        "discount"         => $variation->discount,
                        "sell_price"       => $variation->sell_price,
                        "offer_percent"    => $variation->offer_percent,
                        "img_path"         => Helper::getFilePath($variation->img_path),
                        "attribute_value1" => $variation->attributeValue1,
                        "attribute_value2" => $variation->attributeValue2,
                        "attribute_value3" => $variation->attributeValue3
                    ];
                }),
            ];
        });

        return $products;
    }
    
    public function search($request)
    {
        $searchKey = $request->input("search_key", null);

        $products = $this->model->select("id", "name", "slug", "brand_id", "category_id", "mrp", "offer_price", "discount","sell_price","offer_percent","current_stock","minimum_qty","sku","free_shipping","img_path")
        ->with(["category:id,name,slug", "brand:id,name,slug"])
        ->where("status", StatusEnum::ACTIVE)
        ->when($searchKey, fn ($query) => $query->where("name", "like", "%$searchKey%"))
        ->orderBy("name", "ASC")
        ->limit(10)
        ->get()
        ->map(function ($item) {
            return [
                "id"            => $item->id,
                "name"          => $item->name,
                "slug"          => $item->slug,
                "sku"           => $item->sku,
                "mrp"           => $item->mrp,
                "offer_price"   => $item->offer_price,
                "discount"      => $item->discount,
                "sell_price"    => $item->sell_price,
                "offer_percent" => $item->offer_percent,
                "current_stock" => $item->current_stock,
                "minimum_qty"   => $item->minimum_qty,
                "free_shipping" => $item->free_shipping,
                "img_path"      => Helper::getFilePath($item->img_path),
                "category_name" => @$item->category->name,
                "brand_name"    => @$item->brand->name
            ];
        });

        return $products;
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $product = new $this->model();

            $discount     = 0;
            $offerPercent = 0;
            if ($request->offer_price > 0 && $request->offer_price < $request->mrp) {
                $discount = $request->mrp - $request->offer_price;
                $offerPercent = ($discount * 100) / $request->mrp;
            }

            $sellPrice = $request->offer_price > 0 ? $request->offer_price : $request->mrp;

            $product->name                = preg_replace('/\s+/', ' ', trim($request->name));
            $product->slug                = Str::slug($request->name) . '-' . Str::random(6);
            $product->brand_id            = $request->brand_id;
            $product->category_id         = $request->category_id;
            $product->sub_category_id     = $request->sub_category_id;
            $product->sub_sub_category_id = $request->sub_sub_category_id;
            $product->product_type_id     = $request->product_type_id;
            $product->buy_price           = $request->buy_price ?? 0;
            $product->mrp                 = $request->mrp ?? 0;
            $product->offer_price         = $request->offer_price ?? 0;
            $product->sell_price          = $sellPrice;
            $product->discount            = $discount;
            $product->offer_percent       = $offerPercent;
            $product->current_stock       = $request->current_stock;
            $product->total_sell_qty      = $request->total_sell_qty ?? 0;
            $product->alert_qty           = $request->alert_qty;
            $product->minimum_qty         = $request->minimum_qty;
            $product->status              = $request->status;
            $product->sku                 = $request->sku;
            $product->free_shipping       = $request->free_shipping ?? 0;
            $product->video_url           = $request->video_url;
            $product->description         = $request->description;
            $product->short_description   = $request->short_description;
            $product->meta_keywords       = $request->meta_keywords;
            $product->meta_title          = $request->meta_title;
            $product->meta_description    = $request->meta_description;
            $product->save();


            if ($request->variations && count($request->variations) > 0) {
                foreach ($request->variations as $key => $variation) {
                    // Calculate variation discount and offer percent
                    $variationDiscount     = 0;
                    $variationOfferPercent = 0;
                    $variationSellPrice    = 0;
                    $variationMrp          = $variation["mrp"] ?? 0;
                    $variationOfferPrice   = $variation["offer_price"] ?? 0;
                    $description           = $variation["description"] ?? null;

                    if ($variationOfferPrice > 0 && $variationOfferPrice < $variationMrp) {
                        $variationDiscount     = $variationMrp - $variationOfferPrice;
                        $variationOfferPercent = ($variationDiscount * 100) / $variationMrp;
                        $variationSellPrice    = $variationOfferPrice;
                    } else {
                        $variationSellPrice    = $variationMrp;
                    }

                    // Upload product variation image
                    $variationImgPath = null;
                    if ($request->hasFile("variations." . $key . ".image")) {
                        $variationImage = $request->file("variations." . $key . ".image");

                        $variationImgPath = Helper::uploadImage($variationImage, $product->variationUploadPath, $request->height, $request->width);
                    }

                    ProductVariation::create([
                        "product_id"           => $product->id,
                        "attribute_value_id_1" => !empty($variation["attribute_value_id_1"]) ? $variation["attribute_value_id_1"] : null,
                        "attribute_value_id_2" => !empty($variation["attribute_value_id_2"]) ? $variation["attribute_value_id_2"] : null,
                        "attribute_value_id_3" => !empty($variation["attribute_value_id_3"]) ? $variation["attribute_value_id_3"] : null,
                        "current_stock"        => $variation["current_stock"] ?? 0,
                        "is_default"           => $variation["is_default"] ?? 0,
                        "buy_price"            => $variation["buy_price"] ?? 0,
                        "mrp"                  => $variationMrp,
                        "offer_price"          => $variationOfferPrice,
                        "discount"             => $variationDiscount,
                        "offer_percent"        => $variationOfferPercent,
                        "sell_price"           => $variationSellPrice,
                        "img_path"             => $variationImgPath,
                        "description"          => $description,
                    ]);
                }
            }

            // Upload product image
            if ($request->hasFile("image")) {
                $product->img_path =  Helper::uploadImage($request->image, $product->uploadPath, $request->height, $request->width);
                $product->save();
            }

            // Upload gallery image
            if ($request->hasFile("gallery_images")) {
                foreach ($request->file("gallery_images") as $image) {
                    GalleryImage::create([
                        "product_id" => $product->id,
                        "img_path"   => Helper::uploadImage($image, GalleryImage::getUploadPath(), $request->height, $request->width),
                    ]);
                }
            }

            if ($request->hasFile("review_images")) {
                foreach ($request->file("review_images") as $reviewImage) {
                    ReviewImage::create([
                        "product_id" => $product->id,
                        "img_path"   => Helper::uploadImage($reviewImage, ReviewImage::getUploadPath(), $request->height, $request->width),
                    ]);
                }
            }

            $product->load(["brand:id,name", "category:id,name", "variations"]);

            return $product;
        });
    }

    public function show($id, $status = null)
    {
        $product = $this->model
            ->with([
            "category:id,name,slug",
            "subCategory:id,name,slug",
            "subSubCategory:id,name,slug",
            "brand:id,name,slug",
            "productType:id,name",
            "reviews",
            "reviewImages",
            "productReviews:id,product_id,name,email,title,image,rating,review,created_at,updated_at",
            "productReviews.reply:id,product_review_id,reply,created_at",
            "productReviews.reply",
            "downSells" => fn($q) => $q->valid(),
            "variations" => function ($query) {
                $query->with([
                    "attributeValue1:id,value,attribute_id",
                    "attributeValue2:id,value,attribute_id",
                    "attributeValue3:id,value,attribute_id",
                    "attributeValue1.attribute:id,name",
                    "attributeValue2.attribute:id,name",
                    "attributeValue3.attribute:id,name"
                ]);
            },
            "images",
            "upSellProducts" => function ($query) {
                $query->with([
                    "category:id,name",
                    "brand:id,name",
                    "variations" => function ($query) {
                        $query->with([
                            "attributeValue1:id,value,attribute_id",
                            "attributeValue2:id,value,attribute_id",
                            "attributeValue3:id,value,attribute_id",
                            "attributeValue1.attribute:id,name",
                            "attributeValue2.attribute:id,name",
                            "attributeValue3.attribute:id,name",
                        ]);
                    }
                ]);
            }
        ])
        ->when($status, fn($query) => $query->where("status", $status))
        ->where(fn($q) => $q->where("slug", $id)->orWhere("id", $id))
        ->first();

        if (!$product) {
            throw new CustomException("Product not found");
        }

        return $product;
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $product = $this->model->find($id);

            if (!$product) {
                throw new CustomException("Product not found");
            }

            $discount     = 0;
            $offerPercent = 0;
            if ($request->offer_price > 0 && $request->offer_price < $request->mrp) {
                $discount = $request->mrp - $request->offer_price;
                $offerPercent = ($discount * 100) / $request->mrp;
            }

            $sellPrice = $request->offer_price > 0 ? $request->offer_price : $request->mrp;

            $product->name                = preg_replace('/\s+/', ' ', trim($request->name));
            $product->brand_id            = $request->brand_id;
            $product->category_id         = $request->category_id;
            $product->sub_category_id     = $request->sub_category_id;
            $product->sub_sub_category_id = $request->sub_sub_category_id;
            $product->product_type_id     = $request->product_type_id;
            $product->buy_price           = $request->buy_price ?? 0;
            $product->mrp                 = $request->mrp ?? 0;
            $product->offer_price         = $request->offer_price ?? 0;
            $product->sell_price          = $sellPrice;
            $product->offer_percent       = $offerPercent;
            $product->discount            = $discount;
            $product->current_stock       = $request->current_stock ?? 0;
            $product->total_sell_qty      = $request->total_sell_qty ?? 0;
            $product->alert_qty           = $request->alert_qty;
            $product->minimum_qty         = $request->minimum_qty;
            $product->status              = $request->status;
            $product->sku                 = $request->sku;
            $product->free_shipping       = $request->free_shipping ?? 0;
            $product->video_url           = $request->video_url;
            $product->description         = $request->description;
            $product->short_description   = $request->short_description;
            $product->meta_keywords       = $request->meta_keywords;
            $product->meta_title          = $request->meta_title;
            $product->meta_description    = $request->meta_description;
            $product->save();

            if ($request->variations && count($request->variations) > 0) {
                // Delete old variation image
                if ($request->delete_variation_image_ids && count($request->delete_variation_image_ids) > 0) {
                    $deleteVariationImages = ProductVariation::whereIn("id", $request->delete_variation_image_ids)->get();

                    if (!empty($deleteVariationImages)) {
                        foreach ($deleteVariationImages as $deleteVariationImage) {
                            if ($deleteVariationImage->img_path) {
                                Helper::deleteFile($deleteVariationImage->img_path);
                            }
                        }
                    }
                }

                $variationDetails           = [];
                foreach ($request->variations as $key => $variation) {
                    // Calculate variation discount and offer percent
                    $variationDiscount     = 0;
                    $variationOfferPercent = 0;
                    $variationSellPrice    = 0;
                    $variationCurrentStock = $variation["current_stock"];
                    $variationMrp          = $variation["mrp"];
                    $variationOfferPrice   = $variation["offer_price"];
                    $variationImagePath    = @$variation["image"];
                    $description           = @$variation["description"];

                    if ($variationOfferPrice > 0 && $variationOfferPrice < $variationMrp) {
                        $variationDiscount     = $variationMrp - $variationOfferPrice;
                        $variationOfferPercent = ($variationDiscount * 100) / $variationMrp;
                        $variationSellPrice    = $variationOfferPrice;
                    } else {
                        $variationSellPrice   = $variationMrp;
                    }

                    // Upload product variation image
                    if ($request->hasFile("variations." . $key . ".image")) {
                        $variationImage = $request->file("variations." . $key . ".image");

                        $variationImgPath = Helper::uploadImage($variationImage, $product->variationUploadPath, $request->height, $request->width);
                    } else {
                        $variationImagePath = parse_url($variationImagePath, PHP_URL_PATH);
                        $variationImgPath = ltrim($variationImagePath, '/');
                    }

                    $variationDetails[] = [
                        "product_id"           => $product->id,
                        "attribute_value_id_1" => !empty($variation["attribute_value_id_1"]) ? $variation["attribute_value_id_1"] : null,
                        "attribute_value_id_2" => !empty($variation["attribute_value_id_2"]) ? $variation["attribute_value_id_2"] : null,
                        "attribute_value_id_3" => !empty($variation["attribute_value_id_3"]) ? $variation["attribute_value_id_3"] : null,
                        "is_default"           => $variation["is_default"] ?? 0,
                        "buy_price"            => $variation["buy_price"],
                        "mrp"                  => $variation["mrp"],
                        "offer_price"          => $variation["offer_price"] ?? 0,
                        "current_stock"        => $variationCurrentStock,
                        "discount"             => $variationDiscount,
                        "offer_percent"        => $variationOfferPercent,
                        "sell_price"           => $variationSellPrice,
                        "img_path"             => $variationImgPath,
                        "description"          => $description,
                    ];
                }

                // Delete old variation
                $product->variations()->delete();

                // Insert new variation
                ProductVariation::insert($variationDetails);
            } else {
                // Delete all variation image and variation
                foreach ($product->variations as $variation) {
                    Helper::deleteFile($variation->img_path);
                }

                $product->variations()->delete();
            }

            // Upload product image
            if ($request->hasFile("image")) {
                $product->img_path = Helper::uploadImage($request->image, $product->uploadPath, $request->height, $request->width, $product->img_path);
                $product->save();
            }

            // Delete old gallery image
            if (!empty($request->delete_gallery_image_ids)) {
                // Get old image
                $oldImages = GalleryImage::whereIn("id", $request->delete_gallery_image_ids)->get();

                foreach ($oldImages as $oldImage) {
                    Helper::deleteFile($oldImage->img_path);
                }

                GalleryImage::whereIn("id", $request->delete_gallery_image_ids)->delete();
            }

            // Upload gallery new image
            if ($request->hasFile("gallery_images")) {
                $galleryImages = $request->file("gallery_images");

                foreach ($galleryImages as $key => $image) {
                    GalleryImage::create([
                        "product_id" => $product->id,
                        "img_path"   =>  Helper::uploadImage($image, GalleryImage::getUploadPath(), $request->height, $request->width),
                    ]);
                }
            }

            // Delete old Review image
            if (!empty($request->delete_review_image_ids)) {
                // Get old image
                $oldReviewImages = ReviewImage::whereIn("id", $request->delete_review_image_ids)->get();

                foreach ($oldReviewImages as $oldReviewImage) {
                    Helper::deleteFile($oldReviewImage->img_path);
                }

                ReviewImage::whereIn("id", $request->delete_review_image_ids)->delete();
            }

            // Upload Review new image
            if ($request->hasFile("review_images")) {
                $reviewImages = $request->file("review_images");

                foreach ($reviewImages as $key => $reviewImage) {
                    ReviewImage::create([
                        "product_id" => $product->id,
                        "img_path"   =>  Helper::uploadImage($reviewImage, ReviewImage::getUploadPath(), $request->height, $request->width),
                    ]);
                }
            }

            $product->load(["brand:id,name", "category:id,name", "variations"]);

            return $product;
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $product = $this->model->find($id);

            if (!$product) {
                throw new CustomException("Product not found");
            }

            //  Delete old image
            if ($product->img_path) {
                Helper::deleteFile($product->img_path);
            }

            foreach ($product->images as $image) {
                Helper::deleteFile($image->img_path);
            }

            foreach ($product->variations as $variation) {
                Helper::deleteFile($variation->img_path);
            }

            foreach ($product->reviews as $review) {
                Helper::deleteFile($review->img_path);
            }

            $product->images()->delete();
            $product->upSellProducts()->delete();
            $product->sectionProducts()->delete();
            $product->reviews()->delete();
            $product->campaignProducts()->delete();
            $product->incompleteProducts()->delete();

            if (Helper::isModuleActive("LandingPage")) {
                $product->landingPageItems()->delete();
            }

            return $product->delete();
        });
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);
        $categorySlug = $request->input("category_slugs", null);
        $brandId      = $request->input("brand_id", null);
        $categoryId   = $request->input("category_id", null);

        $products = $this->model->onlyTrashed()->with([
            "category:id,name,slug",
            "subCategory:id,name,slug",
            "brand:id,name,slug",
            "variations",
            "variations.attributeValue1:id,value",
            "variations.attributeValue2:id,value",
            "variations.attributeValue3:id,value"
        ])
        ->when($searchKey, function ($query) use ($searchKey) {
            $query->where("name", "like", "%$searchKey%")
                ->orWhere("sku", "like", "%$searchKey%");
        })
        ->when($status, fn($query) => $query->where("status", "like", "$status"))
        ->when($categoryId, fn ($query) => $query->where("category_id", "like", "$categoryId"))
        ->when($categorySlug, function ($query) use ($categorySlug) {
            $query->whereHas("category", fn($q) => $q->where("slug", $categorySlug));
        })
        ->when($brandId, fn($query) => $query->where("brand_id", "like", "$brandId"))
        ->orderBy("created_at", "desc")
        ->paginate($paginateSize);

        return $products;
    }

    public function restore($id)
    {
        $product = $this->model->onlyTrashed()->find($id);

        if (!$product) {
            throw new CustomException("Product not found", 404);
        }

        return $product->restore();
    }

    public function permanentDelete($id)
    {
        $product = $this->model->onlyTrashed()->find($id);

        if (!$product) {
            throw new CustomException("Product not found", 404);
        }

        $product->variations()->delete();

        return $product->forceDelete();
    }

    public function copy($id)
    {
        return DB::transaction(function () use ($id) {
            $product = $this->model->find($id);

            if (!$product) {
                throw new CustomException("Product not found");
            }

            $copyProduct = new $this->model();

            $copyProduct->name                = $product->name . " copy";
            $copyProduct->slug                = $product->name . "-copy";
            $copyProduct->brand_id            = $product->brand_id;
            $copyProduct->category_id         = $product->category_id;
            $copyProduct->sub_category_id     = $product->sub_category_id;
            $copyProduct->sub_sub_category_id = $product->sub_sub_category_id;
            $copyProduct->product_type_id     = $product->product_type_id;
            $copyProduct->buy_price           = $product->buy_price ?? 0;
            $copyProduct->mrp                 = $product->mrp ?? 0;
            $copyProduct->offer_price         = $product->offer_price ?? 0;
            $copyProduct->sell_price          = $product->sell_price;
            $copyProduct->discount            = $product->discount;
            $copyProduct->offer_percent       = $product->offer_percent;
            $copyProduct->current_stock       = $product->current_stock;
            $copyProduct->alert_qty           = $product->alert_qty;
            $copyProduct->minimum_qty         = $product->minimum_qty;
            $copyProduct->status              = $product->status;
            $copyProduct->sku                 = $product->sku;
            $copyProduct->free_shipping       = $product->free_shipping ?? 0;
            $copyProduct->video_url           = $product->video_url;
            $copyProduct->img_path            = $product->img_path;
            $copyProduct->description         = $product->description;
            $copyProduct->short_description   = $product->short_description;
            $copyProduct->meta_keywords       = $product->meta_keywords;
            $copyProduct->meta_title          = $product->meta_title;
            $copyProduct->meta_description    = $product->meta_description;
            $copyProduct->save();

            // Attach  and up sell with product
            $copyProduct->upSellProducts()->sync($product->upSellProducts->pluck("id")->toArray());

            if ($product->variations && count($product->variations) > 0) {
                foreach ($product->variations as $variation) {
                    ProductVariation::create([
                        "product_id"           => $copyProduct->id,
                        "attribute_value_id_1" => $variation->attribute_value_id_1,
                        "attribute_value_id_2" => $variation->attribute_value_id_2,
                        "attribute_value_id_3" => $variation->attribute_value_id_3,
                        "current_stock"        => $variation->current_stock,
                        "is_default"           => $variation->is_default,
                        "buy_price"            => $variation->buy_price,
                        "mrp"                  => $variation->mrp,
                        "offer_price"          => $variation->offer_price,
                        "discount"             => $variation->discount,
                        "offer_percent"        => $variation->offer_percent,
                        "sell_price"           => $variation->sell_price,
                        "img_path"             => $variation->img_path,
                        "description"          => $variation->description,
                    ]);
                }
            }

            // Upload gallery image
            if (count($product->images) > 0) {
                foreach ($product->images as $image) {
                    GalleryImage::create([
                        "product_id" => $copyProduct->id,
                        "img_path"   => $image->img_path,
                    ]);
                }
            }

            if (count($product->reviewImages) > 0) {
                foreach ($product->reviewImages as $reviewImage) {
                    ReviewImage::create([
                        "product_id" => $copyProduct->id,
                        "img_path"   => $reviewImage->img_path,
                    ]);
                }
            }

            $copyProduct->load(["brand:id,name", "category:id,name", "variations"]);

            return $copyProduct;
        });
    }

    public function bulkDelete($request)
    {
        return DB::transaction(function () use ($request) {
            $products = $this->model->whereIn('id', $request->product_ids)->get();

            foreach ($products as $product) {
                if ($product->img_path) {
                    Helper::deleteFile($product->img_path);
                }

                foreach ($product->images as $image) {
                    Helper::deleteFile($image->img_path);
                }

                foreach ($product->variations as $variation) {
                    Helper::deleteFile($variation->img_path);
                }

                $product->images()->delete();
                $product->upSellProducts()->delete();
                $product->sectionProducts()->delete();
                $product->campaignProducts()->delete();
                $product->incompleteProducts()->delete();


                if (Helper::isModuleActive("LandingPage")) {
                    $product->landingPageItems()->delete();
                }

                $product->delete();
            }

            return true;
        });
    }

    public function bulkRestore($request)
    {
        return (bool) $this->model
            ->onlyTrashed()
            ->whereIn('id', $request->product_ids)
            ->restore();
    }

    public function bulkPermanentDelete($request)
    {
        return DB::transaction(function () use ($request) {
            $products = $this->model->whereIn('id', $request->product_ids)->get();

            foreach ($products as $product) {
                if ($product->img_path) {
                    Helper::deleteFile($product->img_path);
                }

                foreach ($product->images as $image) {
                    Helper::deleteFile($image->img_path);
                }

                foreach ($product->variations as $variation) {
                    Helper::deleteFile($variation->img_path);
                }

                $product->variations()->delete();
                $product->images()->delete();
                $product->upSellProducts()->delete();
                $product->sectionProducts()->delete();
                $product->campaignProducts()->delete();
                $product->incompleteProducts()->delete();

                if (Helper::isModuleActive("LandingPage")) {
                    $product->landingPageItems()->delete();
                }

                return $product->forceDelete();
            }
        });
    }

    public function productHistory($request, $id)
    {
        $limit = $request->input("limit", 5);

        return $this->model->find($id)->audits()->with("user:id,username")->take($limit)->get();
    }

    public function updateStatus($request)
    {
        foreach ($request->product_ids as $id) {
            $product = $this->model->find($id);

            if (!$product) {
                throw new CustomException("Product not found");
            }

            $product->status = $request->status;
            $product->save();
        }

        return true;
    }

    public function categoryWiseProduct($request, $slug)
    {
        $paginateSize = Helper::checkPaginateSize($request);

        $products = $this->model->with([
            "category:id,name",
            "subCategory:id,name,slug",
            "subSubCategory:id,name,slug",
            "brand:id,name",
            "variations",
            "variations.attributeValue1:id,value",
            "variations.attributeValue2:id,value",
            "variations.attributeValue3:id,value"
        ])
        ->where("status", StatusEnum::ACTIVE)
        ->whereHas("category", fn($query) => $query->where("slug", $slug))
        ->paginate($paginateSize);

        return $products;
    }

    public function subCategoryWiseProduct($request, $slug)
    {
        $paginateSize = Helper::checkPaginateSize($request);

        $products = $this->model->with([
            "category:id,name",
            "subCategory:id,name,slug",
            "subSubCategory:id,name,slug",
            "brand:id,name",
            "variations",
            "variations.attributeValue1:id,value",
            "variations.attributeValue2:id,value",
            "variations.attributeValue3:id,value"
        ])
        ->where("status", StatusEnum::ACTIVE)
        ->whereHas("subCategory", fn ($query) => $query->where("slug", $slug))
        ->paginate($paginateSize);

        return $products;
    }

    public function shopSidebarData()
    {
        $maxSellPrice = Product::max("sell_price");
        $minSellPrice = Product::min("sell_price");

        $categories = Category::withCount("products")
        ->with([
            "subCategories" => function ($query) {
                $query->withCount("products")
                    ->where("status", StatusEnum::ACTIVE)
                    ->having("products_count", ">", 0);
            },
            "subCategories.subSubCategories" => function ($query) {
                $query->withCount("products")
                    ->where("status", StatusEnum::ACTIVE)
                    ->having("products_count", ">", 0);
            }
        ])
        ->where("status", StatusEnum::ACTIVE)
        ->having("products_count", ">", 0)
        ->get();

        $brands = Brand::withCount("products")
        ->where("status", StatusEnum::ACTIVE)
        ->having("products_count", ">", 0)
        ->get();

        $attributes = Attribute::with([
            "attributeValues" => function ($query) {
                $query->withCount([
                    "productVariations1 as product_count_1" => function ($q) {
                        $q->whereNotNull("attribute_value_id_1");
                    },
                    "productVariations2 as product_count_2" => function ($q) {
                        $q->whereNotNull("attribute_value_id_2");
                    },
                    "productVariations3 as product_count_3" => function ($q) {
                        $q->whereNotNull("attribute_value_id_3");
                    }
                ]);
            }
        ])
        ->where("status", StatusEnum::ACTIVE)
        ->orderBy("created_at", "asc")
        ->get();

        return [
            "max_sell_price" => $maxSellPrice,
            "min_sell_price" => $minSellPrice,
            "categories"     => $categories,
            "brands"         => $brands,
            "attributes"     => $attributes,
        ];
    }

    public function stockReport($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);

        $stockReports = $this->model
        ->select(
            "id", "name", "buy_price", "mrp", "offer_price", "discount", "sell_price", "offer_percent", "total_purchase_qty", "total_sell_qty", "current_stock",
            "minimum_qty", "alert_qty", "status", "img_path", "created_at"
        )
        ->when($searchKey, fn ($query) => $query->where("name", "like", "%$searchKey%"))
        ->orderBy("status", "ASC")
        ->orderBy("created_at", "DESC")
        ->paginate($paginateSize);

        return $stockReports;
    }

    public function variationCurrentStock($id)
    {
        $productVariation = ProductVariation::with([
            "attributeValue1:id,value,attribute_id",
            "attributeValue2:id,value,attribute_id",
            "attributeValue3:id,value,attribute_id",
            "attributeValue1.attribute:id,name",
            "attributeValue2.attribute:id,name",
            "attributeValue3.attribute:id,name",
        ])
        ->where("product_id", $id)
        ->get();

        if (!$productVariation) {
            throw new CustomException("Product variation not found");
        }

        return $productVariation;
    }

    public function productVariation($request)
    {
        $variationData     = null;
        $allAttributeData  = [];
        $attributeId1      = $request->input("attribute_id_1", null);
        $attributeId2      = $request->input("attribute_id_2", null);
        $attributeId3      = $request->input("attribute_id_3", null);
        $attributeValueId1 = $request->input("attribute_value_id_1", null);
        $attributeValueId2 = $request->input("attribute_value_id_2", null);
        $attributeValueId3 = $request->input("attribute_value_id_3", null);

        $variations = ProductVariation::with([
            "attributeValue1:id,value,attribute_id",
            "attributeValue2:id,value,attribute_id",
            "attributeValue3:id,value,attribute_id",
            "attributeValue1.attribute:id,name",
            "attributeValue2.attribute:id,name",
            "attributeValue3.attribute:id,name",
        ])
        ->where("product_id", $request->product_id)
        ->when($attributeValueId1, fn ($query) => $query->where("attribute_value_id_1", $attributeValueId1))
        ->when($attributeValueId2, fn ($query) => $query->where("attribute_value_id_2", $attributeValueId2))
        ->when($attributeValueId3, fn ($query) => $query->where("attribute_value_id_3", $attributeValueId3))
        ->get();

        // Get variation product price
        if (count($variations) === 1) {
            $variationData = $variations->map(function ($variation) {
                return [
                    "mrp"           => $variation["mrp"],
                    "offer_price"   => $variation["offer_price"],
                    "discount"      => $variation["discount"],
                    "sell_price"    => $variation["sell_price"],
                    "offer_percent" => $variation["offer_percent"],
                ];
            });
        }

        $allData = ProductVariation::with([
            "attributeValue1:id,value,attribute_id",
            "attributeValue2:id,value,attribute_id",
            "attributeValue3:id,value,attribute_id",
            "attributeValue1.attribute:id,name",
            "attributeValue2.attribute:id,name",
            "attributeValue3.attribute:id,name",
        ])
        ->where("product_id", $request->product_id)
        ->when($attributeValueId1 && !$attributeId1, fn ($query) => $query->where("attribute_value_id_1", $attributeValueId1))
        ->when($attributeValueId2 && !$attributeId2, fn ($query) => $query->where("attribute_value_id_2", $attributeValueId2))
        ->when($attributeValueId3 && !$attributeId3, fn ($query) => $query->where("attribute_value_id_3", $attributeValueId3))
        ->get();

        $attributes1 = $allData->pluck("attributeValue1")->unique("id")->values();
        $attributes2 = $allData->pluck("attributeValue2")->unique("id")->values();
        $attributes3 = $allData->pluck("attributeValue3")->unique("id")->values();

        if ($attributeId1) {
            $allAttributeData = $this->formatAttributeData($attributes1);
        }

        if ($attributeId2) {
            $allAttributeData = $this->formatAttributeData($attributes2);
        }

        if ($attributeId3) {
            $allAttributeData = $this->formatAttributeData($attributes3);
        }

        $groupAttributes = $this->groupAttributes($variations);

        if (count($allAttributeData) > 0) {
            $groupAttributes = array_merge($groupAttributes, $allAttributeData);
        }

        return [
            "attributes"      => $groupAttributes,
            "variation_price" => $variationData
        ];
    }

    private function groupAttributes($variations)
    {
        $attributes = [];

        foreach ($variations as $variation) {
            // Loop through possible attribute values
            for ($i = 1; $i <= 3; $i++) {
                $attributeValue = $variation->{"attributeValue$i"} ?? null;
                if ($attributeValue) {
                    $attributeName = @$attributeValue->attribute->name;
                    // Check if the attribute name exists in the array
                    if (!isset($attributes[$attributeName])) {
                        $attributes[$attributeName] = [];
                    }
                    // Add unique attributes to the array
                    $attributes[$attributeName][$attributeValue->id] = [
                        "attribute_value_id" => $attributeValue->id,
                        "attribute_value"    => $attributeValue->value,
                        "attribute_id"       => $attributeValue->attribute_id
                    ];
                }
            }
        }

        // Flatten the arrays to ensure that each attribute type is properly formatted
        foreach ($attributes as &$attributeGroup) {
            $attributeGroup = array_values($attributeGroup);
        }

        return $attributes;
    }

    private function formatAttributeData($data)
    {
        $attribute = [];

        foreach ($data as $item) {
            $attributeName = @$item->attribute->name;
            if ($attributeName) {
                $attribute[$attributeName][] = [
                    "attribute_value_id" => $item->id,
                    "attribute_value"    => $item->value,
                    "attribute_id"       => $item->attribute_id
                ];
            }
        }

        return $attribute;
    }

    public function updateProductSlug($request, $id)
    {
        $product = $this->model->find($id);

        if (!$product) {
            throw new CustomException("Product not found");
        }

        $product->slug = $request->slug;

        $product->save();

        return $product;
    }

    public function checkProductSlug($request)
    {
        $data = $this->model->where("slug", $request->slug)->first();

        return $data ? true : false;
    }
}
