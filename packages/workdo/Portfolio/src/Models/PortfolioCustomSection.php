<?php

namespace Workdo\Portfolio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PortfolioCustomSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'title',
        'content',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
        ];
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}
