<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;

class BeautySubscriber extends Model
{
    protected $fillable = [
        'email',
        'created_by'
    ];

}