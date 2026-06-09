<?php

namespace Workdo\BulkSMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BulksmsSendMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'group_id',
        'mobile_no',
        'sms',
        'status',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'group_id' => 'integer',
            'status' => 'string',
        ];
    }
}