<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BeautyLoyaltyProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'points_earned',
        'points_redeemed',
        'last_updated',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'last_updated' => 'date'
        ];
    }
}