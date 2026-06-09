<?php

namespace Workdo\Rotas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class RotasAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'start_date',
        'end_date',
        'availability',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'availability' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}