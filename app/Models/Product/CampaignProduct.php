<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignProduct extends Model
{
    protected $guarded = ["id"];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, "campaign_id", "id");
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

    public function campaignProductVariations(): HasMany
    {
        return $this->hasMany(CampaignProductVariation::class, "campaign_product_id", "id");
    }
}
