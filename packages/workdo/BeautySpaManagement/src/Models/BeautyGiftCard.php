<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BeautyGiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_code',
        'customer',
        'balance',
        'expiry_date',
        'status',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'expiry_date' => 'date',
            'status' => 'boolean'
        ];
    }
}