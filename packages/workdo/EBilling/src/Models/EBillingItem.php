<?php

namespace Workdo\EBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EBillingItem extends Model
{
    use HasFactory;

    protected $table = 'ebilling_items';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}