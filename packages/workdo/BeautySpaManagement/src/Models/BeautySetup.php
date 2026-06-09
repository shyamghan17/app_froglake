<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;

class BeautySetup extends Model
{
    protected $fillable = [
        'key',
        'value',
        'creator_id',
        'created_by'
    ];

    protected $casts = [
        'creator_id' => 'integer',
        'created_by' => 'integer'
    ];
}