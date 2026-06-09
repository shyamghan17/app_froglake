<?php

namespace Workdo\PhotoStudioManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhotoStudioTeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'designation',
        'experience_year',
        'skills',
        'rate_per_hour',
        'is_active',
        'bio',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active'    => 'boolean',
            'rate_per_hour' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
