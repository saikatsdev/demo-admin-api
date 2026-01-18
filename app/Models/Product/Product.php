<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use App\Models\Order\DownSell;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Order\IncompleteOrderDetail;
use App\Models\Order\OrderDetail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public $uploadPath          = "uploads/products";
    public $variationUploadPath = "uploads/products/variationImage";

    protected $casts = [
        "brand_id"        => "integer",
        "category_id"     => "integer",
        "sub_category_id" => "integer",
        "free_shipping"   => "boolean",
        "is_default"      => "boolean",
    ];

    public function brand() : BelongsTo
    {
        return $this->belongsTo(Brand::class, "brand_id", "id");
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }

    public function subCategory() : BelongsTo
    {
        return $this->belongsTo(SubCategory::class, "sub_category_id", "id");
    }

    public function subSubCategory() : BelongsTo
    {
        return $this->belongsTo(SubSubCategory::class, "sub_sub_category_id", "id");
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, "product_type_id", "id");
    }

    public function variations() : HasMany
    {
        return $this->hasMany(ProductVariation::class, "product_id", "id");
    }

    public function attributeValues1()
    {
        return $this->belongsToMany(AttributeValue::class, "product_variations", "product_id", "attribute_value_id_1")
        ->withTimestamps();
    }

    public function attributeValues2()
    {
        return $this->belongsToMany(AttributeValue::class, "product_variations", "product_id", "attribute_value_id_2")
        ->withTimestamps();
    }

    public function attributeValues3()
    {
        return $this->belongsToMany(AttributeValue::class, "product_variations", "product_id", "attribute_value_id_3")
        ->withTimestamps();
    }

    public function images() : HasMany
    {
        return $this->hasMany(GalleryImage::class, "product_id", "id");
    }

    public function reviewImages() : HasMany
    {
        return $this->hasMany(ReviewImage::class, "product_id", "id");
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, "product_id", "id");
    }

    public function upSellProducts()
    {
        return $this->belongsToMany(Product::class, "up_sell_products", "product_id", "up_sell_id")->withTimestamps();
    }

    public function sectionProducts(): HasMany
    {
        return $this->hasMany(SectionProduct::class, "product_id", "id");
    }

    public function campaignProducts(): HasMany
    {
        return $this->hasMany(CampaignProduct::class, "product_id", "id");
    }

    public function incompleteProducts(): HasMany
    {
        return $this->hasMany(IncompleteOrderDetail::class, "product_id", "id");
    }

    public function downSells(): BelongsToMany
    {
        return $this->belongsToMany(DownSell::class, "down_sell_product", "product_id", "down_sell_id");
    }

    public function thankYouPageOffers(): BelongsToMany
    {
        return $this->belongsToMany(ThankYouPageOffer::class, "thank_you_page_offer_product", "product_id", "thank_you_page_offer_id");
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class)->where('status', 'approved');
    }
}
