<?php

namespace Workdo\SuggestionBox\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class SuggestionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'description',
        'is_active',
        'display_order',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean'
        ];
    }
}