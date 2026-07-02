<?php

namespace Workdo\PettyCashManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashAuditLog extends Model
{
    use HasFactory;

    protected $table = 'petty_cash_audit_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'action',
        'subject_type',
        'subject_id',
        'actor_id',
        'meta',
        'created_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'subject_id' => 'integer',
            'actor_id' => 'integer',
            'meta' => 'array',
            'created_by' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}

