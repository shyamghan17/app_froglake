<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'bank_account_id',
        'reference_number',
        'payment_date',
        'amount',
        'payment_status',
        'notes',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
            'payment_status' => 'string',
        ];
    }

    public function appointment()
    {
        return $this->belongsTo(BookingAppointment::class, 'appointment_id');
    }
}
