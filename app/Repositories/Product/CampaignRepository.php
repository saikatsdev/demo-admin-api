<?php

namespace App\Repositories\Product;

use Exception;
use App\Helpers\Helper;
use App\Models\Product\Product;
use App\Models\Product\Campaign;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Models\Product\CampaignProduct;
use App\Models\Product\CampaignProductVariation;

class CampaignRepository
{
    public function __construct(protected Campaign $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $paginateSize = $request->input("paginate_size", null);
        $now          = $request->input("now", null);
        $status       = $request->input("status", null);

        try {
            $campaign = $this->model
                ->when($now, fn($query) => $query->where("start_date", "<=", $now)->where("end_date", ">=", $now))
                ->when($status, fn($query) => $query->where("status", $status))
                ->orderBy("created_at", "desc")
                ->paginate($paginateSize);

            return $campaign;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $campaign = new $this->model();

            $campaign->title      = $request->title;
            $campaign->slug       = $request->title;
            $campaign->start_date = $request->start_date;
            $campaign->end_date   = $request->end_date;
            $campaign->status     = $request->status;

            // Upload image
            if ($request->image) {
                $campaign->img_path = Helper::uploadFile($request->image, $campaign->uploadPath, $request->height, $request->width);
                $campaign->width = $request->width;
                $campaign->height = $request->height;
            }

            $campaign->save();

            foreach ($request->items as $item) {
                $product = Product::select("id", "buy_price", "mrp", "sell_price", "discount")
                    ->with("variations")
                    ->find($item["product_id"]);

                if (!$product) {
                    throw new CustomException("Product not found");
                }

                // For discount type fixed
                if ($item["discount_type"] === "fixed") {
                    // For non variation product
                    $discountValue = 0;
                    $offerPrice    = 0;
                    $offerPercent  = 0;
                    if ($product->mrp > 0) {
                        $discountValue = $item["discount"];
                        $offerPrice    = $product->mrp - $item["discount"];
                        $offerPercent = ($discountValue * 100) / $product->mrp;
                    }

                    $campaignProduct = $campaign->campaignProducts()->create(
                        [
                            "campaign_id"    => $campaign->id,
                            "product_id"     => $item["product_id"],
                            "buy_price"      => $product->buy_price,
                            "mrp"            => $product->mrp,
                            "discount"       => $item["discount"],
                            "discount_type"  => $item["discount_type"],
                            "discount_value" => $discountValue,
                            "offer_price"    => $offerPrice,
                            "offer_percent"  => $offerPercent
                        ]
                    );

                    // For variation product
                    if (count($product->variations) > 0) {
                        foreach ($product->variations as $variation) {
                            $variationDiscount     = $item["discount"];
                            $variationOfferPercent = ($variationDiscount * 100) / $variation->mrp;

                            $campaignProduct->campaignProductVariations()->create(
                                [
                                    "campaign_product_id"  => $campaignProduct->id,
                                    "attribute_value_id_1" => $variation->attribute_value_id_1,
                                    "attribute_value_id_2" => $variation->attribute_value_id_2,
                                    "attribute_value_id_3" => $variation->attribute_value_id_3,
                                    "is_default"           => $variation->is_default,
                                    "buy_price"            => $variation->buy_price,
                                    "mrp"                  => $variation->mrp,
                                    "offer_price"          => $variation->mrp - $variationDiscount,
                                    "discount"             => $variationDiscount,
                                    "offer_percent"        => $variationOfferPercent,
                                    "img_path"             => $variation->img_path
                                ]
                            );
                        }
                    }
                } else {
                    // For discount type percentage
                    // For non variation product
                    $discountValue = 0;
                    $offerPrice    = 0;
                    if ($product->mrp > 0) {
                        $discountValue = ($item["discount"] * $product->mrp) / 100;
                        $offerPrice    = $product->mrp - $discountValue;
                    }

                    $campaignProduct = $campaign->campaignProducts()->create(
                        [
                            "campaign_id"    => $campaign->id,
                            "product_id"     => $item["product_id"],
                            "buy_price"      => $product->buy_price,
                            "mrp"            => $product->mrp,
                            "discount"       => $item["discount"],
                            "discount_type"  => $item["discount_type"],
                            "discount_value" => $discountValue,
                            "offer_price"    => $offerPrice,
                            "offer_percent"  => $item["discount"]
                        ]
                    );

                    // For variation product
                    if (count($product->variations) > 0) {
                        foreach ($product->variations as $variation) {
                            $variationDiscount = ($item["discount"] * $variation->mrp) / 100;

                            $campaignProduct->campaignProductVariations()->create(
                                [
                                    "campaign_product_id"  => $campaignProduct->id,
                                    "attribute_value_id_1" => $variation->attribute_value_id_1,
                                    "attribute_value_id_2" => $variation->attribute_value_id_2,
                                    "attribute_value_id_3" => $variation->attribute_value_id_3,
                                    "is_default"           => $variation->is_default,
                                    "buy_price"            => $variation->buy_price,
                                    "mrp"                  => $variation->mrp,
                                    "offer_price"          => $variation->mrp - $variationDiscount,
                                    "discount"             => $variationDiscount,
                                    "offer_percent"        => $item["discount"],
                                    "img_path"             => $variation->img_path
                                ]
                            );
                        }
                    }
                }
            }

            DB::commit();

            return $campaign;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($slugOrId, $status = null)
    {
        try {
            $campaign = $this->model->with([
                "campaignProducts",
                "campaignProducts.product:id,brand_id,category_id,name,slug,img_path,current_stock",
                "campaignProducts.product.images" => function ($query) {
                    $query->limit(1);
                },
                "campaignProducts.product.brand:id,name,slug",
                "campaignProducts.product.category:id,name,slug",
                "campaignProducts.campaignProductVariations",
                "campaignProducts.campaignProductVariations.attributeValue1:id,value,attribute_id",
                "campaignProducts.campaignProductVariations.attributeValue2:id,value,attribute_id",
                "campaignProducts.campaignProductVariations.attributeValue3:id,value,attribute_id",
                "campaignProducts.campaignProductVariations.attributeValue1.attribute:id,name,slug",
                "campaignProducts.campaignProductVariations.attributeValue2.attribute:id,name,slug",
                "campaignProducts.campaignProductVariations.attributeValue3.attribute:id,name,slug"
            ])
                ->when($status, fn($query) => $query->where("status", $status))
                ->where("id", $slugOrId)
                ->orWhere("slug", $slugOrId)
                ->first();

            if (!$campaign) {
                throw new CustomException("Campaign not found");
            }

            return $campaign;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $campaign = $this->model->find($id);
            if (!$campaign) {
                throw new CustomException("Campaign not found");
            }

            $campaign->title      = $request->title;
            $campaign->slug       = $request->title;
            $campaign->start_date = $request->start_date;
            $campaign->end_date   = $request->end_date;
            $campaign->status     = $request->status;

            // Upload image
            if ($request->image) {
                $campaign->img_path = Helper::uploadFile($request->image, $campaign->uploadPath, $request->height, $request->width);
                $campaign->width = $request->width;
                $campaign->height = $request->height;
            }

            $campaign->save();

            // Delete campaign product and product variations
            $campaign->campaignProducts()->delete();

            if ($campaign) {
                foreach ($request->items as $item) {
                    $product = Product::select("id", "buy_price", "mrp", "sell_price", "discount")
                        ->with("variations")
                        ->find($item["product_id"]);

                    if (!$product) {
                        throw new CustomException("Product not found");
                    }

                    // For discount type fixed
                    if ($item["discount_type"] === "fixed") {
                        // For non variation product
                        $discountValue = 0;
                        $offerPrice    = 0;
                        if ($product->mrp > 0) {
                            $discountValue = $item["discount"];
                            $offerPrice    = $product->mrp - $item["discount"];
                        }

                        $campaignProduct = CampaignProduct::create(
                            [
                                "campaign_id"    => $campaign->id,
                                "product_id"     => $item["product_id"],
                                "buy_price"      => $product->buy_price,
                                "mrp"            => $product->mrp,
                                "discount"       => $item["discount"],
                                "discount_type"  => $item["discount_type"],
                                "discount_value" => $discountValue,
                                "offer_price"    => $offerPrice
                            ]
                        );

                        // For variation product
                        if (count($product->variations) > 0) {
                            foreach ($product->variations as $variation) {
                                $variationDiscount     = $item["discount"];
                                $variationOfferPercent = ($variationDiscount * 100) / $variation->mrp;

                                CampaignProductVariation::create(
                                    [
                                        "campaign_product_id"  => $campaignProduct->id,
                                        "attribute_value_id_1" => $variation->attribute_value_id_1,
                                        "attribute_value_id_2" => $variation->attribute_value_id_2,
                                        "attribute_value_id_3" => $variation->attribute_value_id_3,
                                        "is_default"           => $variation->is_default,
                                        "buy_price"            => $variation->buy_price,
                                        "mrp"                  => $variation->mrp,
                                        "offer_price"          => $variation->mrp - $variationDiscount,
                                        "discount"             => $variationDiscount,
                                        "offer_percent"        => $variationOfferPercent,
                                        "image"                => $variation->image
                                    ]
                                );
                            }
                        }
                    } else {
                        // For discount type percentage
                        // For non variation product
                        $discountValue = 0;
                        $offerPrice    = 0;
                        if ($product->mrp > 0) {
                            $discountValue = ($item["discount"] * $product->mrp) / 100;
                            $offerPrice    = $product->mrp - $discountValue;
                        }

                        $campaignProduct = CampaignProduct::create(
                            [
                                "campaign_id"    => $campaign->id,
                                "product_id"     => $item["product_id"],
                                "buy_price"      => $product->buy_price,
                                "mrp"            => $product->mrp,
                                "discount"       => $item["discount"],
                                "discount_type"  => $item["discount_type"],
                                "discount_value" => $discountValue,
                                "offer_price"    => $offerPrice
                            ]
                        );

                        // For variation product
                        if (count($product->variations) > 0) {
                            foreach ($product->variations as $variation) {
                                $variationDiscount = ($item["discount"] * $variation->mrp) / 100;

                                CampaignProductVariation::create(
                                    [
                                        "campaign_product_id"  => $campaignProduct->id,
                                        "attribute_value_id_1" => $variation->attribute_value_id_1,
                                        "attribute_value_id_2" => $variation->attribute_value_id_2,
                                        "attribute_value_id_3" => $variation->attribute_value_id_3,
                                        "is_default"           => $variation->is_default,
                                        "buy_price"            => $variation->buy_price,
                                        "mrp"                  => $variation->mrp,
                                        "offer_price"          => $variation->mrp - $variationDiscount,
                                        "discount"             => $variationDiscount,
                                        "offer_percent"        => $item["discount"]
                                    ]
                                );
                            }
                        }
                    }
                }
            }

            DB::commit();

            return $campaign;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $campaign = $this->model->find($id);
            if (!$campaign) {
                throw new CustomException("Campaign not found");
            }

            // Delete campaign product and product variations
            $campaign->campaignProducts()->delete();

            return $campaign->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function campaignProductPrice($request)
    {
        try {
            $campaignProductPrice = CampaignProductVariation::select("campaign_product_id", "size_id", "color_id", "mrp", "offer_price", "discount", "offer_percent")
                ->where("campaign_product_id", $request->campaign_product_id)
                ->where("attribute_value_id_1", $request->attribute_value_id_1)
                ->where("attribute_value_id_2", $request->attribute_value_id_2)
                ->where("attribute_value_id_3", $request->attribute_value_id_3)
                ->first();

            if (!$campaignProductPrice) {
                throw new CustomException("Not found");
            }

            return $campaignProductPrice;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function campaignProductDetail($campaignSlug, $productSlug)
    {
        try {
            $campaignProductDetail = CampaignProduct::with(
                [
                    "product:id,name,slug,free_shipping,current_stock,img_path,sku,short_description,description,category_id,brand_id",
                    "product.category:id,name,slug",
                    "product.brand:id,name,slug",
                    "campaignProductVariations",
                    "campaignProductVariations.attributeValue1:id,value,attribute_id",
                    "campaignProductVariations.attributeValue2:id,value,attribute_id",
                    "campaignProductVariations.attributeValue3:id,value,attribute_id",
                    "campaignProductVariations.attributeValue1.attribute:id,name,slug",
                    "campaignProductVariations.attributeValue2.attribute:id,name,slug",
                    "campaignProductVariations.attributeValue3.attribute:id,name,slug"
                ]
            )
                ->whereHas("campaign", fn($query) => $query->where("slug", $campaignSlug))
                ->whereHas("product", fn($query) => $query->where("slug", $productSlug))
                ->first();

            if (!$campaignProductDetail) {
                throw new CustomException("Not found");
            }

            return $campaignProductDetail;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
