<?php

namespace Workdo\NoticeBoard\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NoticeComment extends Model
{
    protected $fillable = [
        'notice_id',
        'user_id',
        'parent_id',
        'comment',
        'creator_id',
        'created_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(NoticeComment::class, 'parent_id')->with('user:id,name,avatar')->latest();
    }
}
