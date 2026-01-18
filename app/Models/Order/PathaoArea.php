<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class PathaoArea extends Model
{
    protected $table = "pathao_areas";
    
    protected $casts = [
        'area_value' => 'array',
    ];

}
