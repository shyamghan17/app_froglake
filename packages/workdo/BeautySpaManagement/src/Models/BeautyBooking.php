<?php

namespace Workdo\BeautySpaManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BeautyBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'service',
        'date',
        'start_time',
        'end_time',
        'person',
        'price',
        'phone_number',
        'gender',
        'reference',
        'notes',
        'payment_option',
        'payment_status',
        'stage_id',
        'status',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'service' => 'integer',
            'date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'price' => 'decimal:2',
            'stage_id' => 'integer',
        ];
    }

    public function beautyService()
    {
        return $this->belongsTo(BeautyService::class, 'service');
    }

    public static function total_amount($person, $price)
    {
        return $person > 0 ? $price * $person : $price;
    }
    public function payments()
    {
        return $this->hasMany(BeautyBookingPayment::class, 'booking_id');
    }

    public static $reference = [
        'Google' => 'Google',
        'Friend' => 'Friend', 
        'Social Media' => 'Social Media',
        'Other' => 'Other',
    ];

    public static $statuses = [
        'Draft' => 'Draft',
        'Open' => 'Open',
        'Invoiced' => 'Invoiced',
        'Closed' => 'Closed',
    ];
}