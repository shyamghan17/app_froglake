<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Workdo\BeautySpaManagement\Models\BeautyService;

class BeautyMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'benefits',
        'price',
        'description',
        'included_services_id',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2'
        ];
    }
    public function included_services()
    {
        return $this->belongsTo(BeautyService::class);
    }
}