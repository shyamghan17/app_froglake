<?php

namespace Workdo\PhotoStudioManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\PhotoStudioManagement\Models\PhotoStudioServiceCategory;

class PhotoStudioService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'service_category_ids',
        'description',
        'image',
        'price',
        'status',
        'camera_kit_ids',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'service_category_ids' => 'array',
            'camera_kit_ids'       => 'array',
            'status'               => 'boolean',
        ];
    }

    public function getCategoryNamesAttribute()
    {
        if (empty($this->service_category_ids)) return [];
        return PhotoStudioServiceCategory::whereIn('id', $this->service_category_ids)->pluck('name');
    }
}
