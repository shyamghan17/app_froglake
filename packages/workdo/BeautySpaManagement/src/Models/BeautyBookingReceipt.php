<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BeautyBookingReceipt extends Model
{
    use HasFactory;



    protected $fillable = [
        'beauty_booking_id',
        'name',
        'service',
        'number',
        'gender',
        'start_time',
        'end_time',
        'price',
        'payment_type',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i:s',
            'end_time' => 'datetime:H:i:s',
            'price' => 'decimal:2',
        ];
    }

    public function beautyBooking()
    {
        return $this->belongsTo(BeautyBooking::class);
    }
}