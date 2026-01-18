<?php

namespace App\Models\Product;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends BaseModel
{
    public $uploadPath = 'uploads/campaigns';

    public function campaignProducts(): HasMany
    {
        return $this->hasMany(CampaignProduct::class, "campaign_id", "id");
    }
}
