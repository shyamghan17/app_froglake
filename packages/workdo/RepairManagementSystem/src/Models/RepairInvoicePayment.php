<?php

namespace Workdo\RepairManagementSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairInvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'repair_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_id' => 'integer',
            'repair_id' => 'integer',
            'amount' => 'decimal:2',
            'payment_date' => 'date'
        ];
    }

    public function repair_invoice()
    {
        return $this->belongsTo(RepairInvoice::class, 'invoice_id');
    }

    public function repair_order()
    {
        return $this->belongsTo(RepairOrderRequest::class, 'repair_id');
    }
}