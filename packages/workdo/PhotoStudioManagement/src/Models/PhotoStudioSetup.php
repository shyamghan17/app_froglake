<?php

namespace Workdo\PhotoStudioManagement\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoStudioSetup extends Model
{
    protected $fillable = [
        'key',
        'value',
        'creator_id',
        'created_by',
    ];

}
