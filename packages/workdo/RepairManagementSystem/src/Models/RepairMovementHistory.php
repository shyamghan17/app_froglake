<?php

namespace Workdo\RepairManagementSystem\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class RepairMovementHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_order_request_id',
        'date_time',
        'movement_from',
        'movement_to',
        'movement_reason',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_time' => 'datetime',
        ];
    }

    public function repairOrderRequest()
    {
        return $this->belongsTo(RepairOrderRequest::class, 'repair_order_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public static function movementHistoryStore($repair_id, $from, $to, $reason)
    {
        self::create([
            'repair_order_request_id' => $repair_id,
            'date_time' => now(),
            'movement_from' => $from,
            'movement_to' => $to,
            'movement_reason' => $reason,
            'creator_id' => Auth::id(),
            'created_by' => creatorId(),
        ]);
    }
}