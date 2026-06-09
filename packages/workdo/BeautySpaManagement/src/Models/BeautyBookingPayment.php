<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workdo\BeautySpaManagement\Models\BeautyBooking;

class BeautyBookingPayment extends Model
{
    protected $fillable = [
        'booking_id',
        'total_person',
        'service',
        'payment_amount',
        'payment_date',
        'description',
        'customer_name',
        'reference_number',
        'bank_account_id',
        'creator_id',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'payment_amount' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(BeautyBooking::class, 'booking_id');
    }

    public function beautyService(): BelongsTo
    {
        return $this->belongsTo(BeautyService::class, 'service');
    }
}
