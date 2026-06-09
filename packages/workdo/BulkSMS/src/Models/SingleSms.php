<?php

namespace Workdo\BulkSMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class SingleSms extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'mobile_number',
        'status',
        'sms',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'contact_id' => 'integer',
            'status' => 'string'
        ];
    }

    public function contact()
    {
        return $this->belongsTo(BulkSmsContact::class, 'contact_id');
    }




}