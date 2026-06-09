<?php

namespace Workdo\PhotoStudioManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentTag;

class PhotoStudioCameraKit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'description',
        'tags',
        'specifications',
        'equipment_type_id',
        'status',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tags'           => 'array',
            'specifications' => 'array',
        ];
    }

    public function equipmentType()
    {
        return $this->belongsTo(PhotoStudioEquipmentType::class, 'equipment_type_id');
    }

    public function getTagNamesAttribute()
    {
        if (empty($this->tags)) return [];
        return PhotoStudioEquipmentTag::whereIn('id', $this->tags)->pluck('name');
    }
}
