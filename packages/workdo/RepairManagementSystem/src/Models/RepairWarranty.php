<?php

namespace Workdo\RepairManagementSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\RepairManagementSystem\Models\RepairOrderRequest;
use Workdo\RepairManagementSystem\Models\RepairPart;

class RepairWarranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_number',
        'warranty_period',
        'warranty_terms',
        'claim_status',
        'repair_order_id',
        'part_id',
        'creator_id',
        'created_by',
    ];

    public function repair_order()
    {
        return $this->belongsTo(RepairOrderRequest::class);
    }

    public function part()
    {
        return $this->belongsTo(RepairPart::class);
    }
}