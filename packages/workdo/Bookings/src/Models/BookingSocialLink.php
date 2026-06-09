<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSocialLink extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'link',
        'created_by',
        'creator_id'
    ];
}