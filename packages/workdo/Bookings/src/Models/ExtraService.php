<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ExtraService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'status',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean'
        ];
    }




}