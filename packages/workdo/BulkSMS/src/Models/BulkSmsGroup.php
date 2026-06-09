<?php

namespace Workdo\BulkSMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BulkSmsGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contacts',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'contacts' => 'array'
        ];
    }




}