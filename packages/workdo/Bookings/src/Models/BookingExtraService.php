<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class BookingExtraService extends Model
{
    protected $fillable = [
        'name',
        'status',
        'created_by',
        'creator_id'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

}