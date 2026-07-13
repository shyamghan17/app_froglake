<?php

namespace Workdo\SuggestionBox\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SuggestionView extends Model
{
    protected $fillable = [
        'suggestion_id',
        'user_id',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
    
    public function suggestion()
    {
        return $this->belongsTo(Suggestion::class, 'suggestion_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}