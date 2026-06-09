<?php

namespace Workdo\PhotoStudioManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhotoStudioContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'message',
        'received_date',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date'
        ];
    }
}