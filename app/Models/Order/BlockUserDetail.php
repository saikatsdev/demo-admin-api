<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlockUserDetail extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function blockUser(): BelongsTo
    {
        return $this->belongsTo(BlockUser::class, "block_user_id", "id");
    }
}
