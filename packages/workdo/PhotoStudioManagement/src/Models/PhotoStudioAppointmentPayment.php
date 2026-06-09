<?php

namespace Workdo\PhotoStudioManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhotoStudioAppointmentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'appointment_number',
        'customer_name',
        'service_name',
        'payment_date',
        'amount',
        'bank_account_id',
        'payment_status',
        'payment_type',
        'description',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'appointment_id' => 'integer',
            'payment_date'   => 'date',
            'amount'         => 'decimal:2',
        ];
    }

    public function appointment()
    {
        return $this->belongsTo(PhotoStudioAppointment::class, 'appointment_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_status)) {
                $payment->payment_status = 'pending';
            }
        });

        static::created(function ($payment) {
            if ($payment->appointment) {
                $payment->appointment->update(['payment_status' => 'confirmed']);
            }
        });
    }
}
