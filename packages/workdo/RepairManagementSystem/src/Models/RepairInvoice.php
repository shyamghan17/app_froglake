<?php

namespace Workdo\RepairManagementSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\RepairManagementSystem\Models\RepairOrderRequest;

class RepairInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'repair_id',
        'repair_charge',
        'total_amount',
        'paid_amount',
        'status',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'repair_id' => 'integer',
            'repair_charge' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2'
        ];
    }



    public function repair_order()
    {
        return $this->belongsTo(RepairOrderRequest::class, 'repair_id');
    }

    public function payments()
    {
        return $this->hasMany(RepairInvoicePayment::class, 'invoice_id');
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->total_paid;
    }
}