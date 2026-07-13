<?php

namespace Workdo\NoticeBoard\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeTarget extends Model
{
    protected $fillable = [
        'notice_id',
        'target_type',
        'department_id',
        'role_id',
        'user_id',
    ];
}
