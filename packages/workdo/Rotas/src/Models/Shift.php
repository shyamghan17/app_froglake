<?php

namespace Workdo\Rotas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_name',
        'start_time',
        'end_time',
        'break_start_time',
        'break_end_time',
        'is_night_shift',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_night_shift' => 'boolean',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'break_start_time' => 'datetime:H:i',
            'break_end_time' => 'datetime:H:i',
        ];
    }



    public function creator()
    {
        return $this->belongsTo(User::class);
    }
}