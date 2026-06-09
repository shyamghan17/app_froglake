<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeautyReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'beauty_services_id',
        'rating',
        'review',
        'creator_id',
        'created_by'
    ];

    public function beautyService()
    {
        return $this->belongsTo(BeautyService::class, 'beauty_services_id');
    }
}