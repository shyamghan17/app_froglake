<?php

namespace Workdo\PettyCashManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_start',
        'period_end',
        'opening_balance',
        'additions_total',
        'expenses_total',
        'expected_closing',
        'counted_cash',
        'variance',
        'locked',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'opening_balance' => 'decimal:2',
            'additions_total' => 'decimal:2',
            'expenses_total' => 'decimal:2',
            'expected_closing' => 'decimal:2',
            'counted_cash' => 'decimal:2',
            'variance' => 'decimal:2',
            'locked' => 'boolean',
            'creator_id' => 'integer',
            'created_by' => 'integer',
        ];
    }
}

