<?php

namespace Workdo\SuggestionBox\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class SuggestionStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'suggestion_id',
        'old_status',
        'new_status',
        'comment',
        'changed_by',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'old_status' => 'string',
            'new_status' => 'string'
        ];
    }

    public function suggestion()
    {
        return $this->belongsTo(Suggestion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}