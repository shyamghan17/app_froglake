<?php

namespace Workdo\SignInWithGoogle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class GoogleUser extends Model
{
    protected $fillable = [
        'google_id',
        'user_id',
        'email',
        'name',
        'avatar',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}