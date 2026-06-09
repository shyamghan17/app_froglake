<?php

namespace Workdo\OpticalAndEyeCareCenter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class EyewearItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_type',
        'brand_name',
        'prescription_detail',
        'numbering_status',
        'customization_details',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer'
        ];
    }
}
