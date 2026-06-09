<?php

namespace Workdo\BeautySpaManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\BeautySpaManagement\Models\BeautyServiceType;

class BeautyService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'max_bookable_persons',
        'price',
        'time',
        'description',
        'service_image',
        'service_type_id',
        'staff_id',
        'included_services',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'service_image' => 'string',
            'included_services' => 'array'
        ];
    }

    public function service_type()
    {
        return $this->belongsTo(BeautyServiceType::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(BeautyBooking::class, 'service');
    }
    public function offers()
    {
        return $this->hasMany(BeautyServiceOffer::class, 'beauty_service_id');
    }
}