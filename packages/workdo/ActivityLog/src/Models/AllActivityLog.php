<?php

namespace Workdo\ActivityLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class AllActivityLog extends Model
{

    protected $fillable = [
        'module',
        'sub_module',
        'description',
        'url',
        'user_id',
        'creator_id',
        'created_by',
    ];   

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }   
}