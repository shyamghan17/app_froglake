<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\BeautySpaManagement\Models\BeautyService;

class BeautyServiceOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'name',
        'price',
        'start_date',
        'end_date',
        'discount',
        'offer_price',
        'description',
        'beauty_service_id',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'discount' => 'decimal:2',
            'offer_price' => 'decimal:2'
        ];
    }



    public function service()
    {
        return $this->belongsTo(BeautyService::class, 'beauty_service_id');
    }
}