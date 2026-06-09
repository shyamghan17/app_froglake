<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Model;

class BookingContact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'creator_id',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}