<?php

namespace Workdo\SuggestionBox\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\SuggestionBox\Models\SuggestionCategory;
use Workdo\SuggestionBox\Models\SuggestionVote;
use Workdo\SuggestionBox\Models\SuggestionView;
use App\Models\User;

class Suggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category_id', 
        'description',
        'is_anonymous',
        'status',
        'admin_response',
        'votes_count',
        'views_count',
        'responded_at',
        'user_id',
        'responded_by',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'category_id'  => 'integer',
            'is_anonymous' => 'boolean',
            'status'       => 'string',
            'votes_count'  => 'integer',
            'views_count'  => 'integer',
            'responded_at' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(SuggestionCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function respondedBy()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function votes()
    {
        return $this->hasMany(SuggestionVote::class, 'suggestion_id');
    }
    
    public function views()
    {
        return $this->hasMany(SuggestionView::class, 'suggestion_id');
    }
}