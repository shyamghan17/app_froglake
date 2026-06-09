<?php

namespace Workdo\BulkSMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BulkSmsContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'mobile_no',
        'city',
        'state',
        'zip_code',
        'creator_id',
        'created_by',
    ];
}