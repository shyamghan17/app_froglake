<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;

class BeautyWorking extends Model
{
    protected $fillable = [
        'opening_time',
        'closing_time',
        'day_of_week',
        'holiday_setting',
        'creator_id',
        'created_by',
    ];

    public static $week_days = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];
}