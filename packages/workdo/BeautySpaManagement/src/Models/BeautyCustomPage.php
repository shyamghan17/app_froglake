<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BeautyCustomPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'contents',
        'description',
        'is_editable',
        'creator_id',
        'created_by',
    ];
    protected $casts = [
        'is_editable' => 'boolean'
    ];
}
