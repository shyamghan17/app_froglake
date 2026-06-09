<?php

namespace Workdo\PhotoStudioManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhotoStudioSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'subscribed_date',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'subscribed_date' => 'date'
        ];
    }
}