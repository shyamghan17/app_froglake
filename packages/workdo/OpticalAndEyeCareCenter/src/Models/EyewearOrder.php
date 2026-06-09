<?php

namespace Workdo\OpticalAndEyeCareCenter\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EyewearOrder extends Model
{
    protected $fillable = [
        'order_number',
        'order_date',
        'patient_id',
        'warehouse_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'payment_status',
        'payment_method',
        'bank_account_id',
        'extra_charge',
        'delivery_date',
        'delivered_at',
        'prescription_details',
        'special_notes',
        'creator_id',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'delivered_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'extra_charge' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(EyePatient::class, 'patient_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(EyewearOrderItem::class, 'order_id');
    }
}
