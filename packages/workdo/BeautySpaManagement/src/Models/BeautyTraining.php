<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BeautyTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_name',
        'trainer',
        'date',
        'duration',
        'location',
        'description',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date'
        ];
    }
}