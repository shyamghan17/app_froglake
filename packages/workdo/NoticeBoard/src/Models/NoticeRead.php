<?php

namespace Workdo\NoticeBoard\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoticeRead extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'notice_id',
        'user_id',
        'read_at',
        'acknowledged_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at'         => 'datetime',
            'acknowledged_at' => 'datetime',
            'created_at'      => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
