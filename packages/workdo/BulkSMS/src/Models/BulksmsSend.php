<?php

namespace Workdo\BulkSMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BulksmsSend extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'mobile_no',
        'sms',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'group_id' => 'integer',
        ];
    }

    public function group()
    {
        return $this->belongsTo(BulkSmsGroup::class, 'group_id');
    }
}