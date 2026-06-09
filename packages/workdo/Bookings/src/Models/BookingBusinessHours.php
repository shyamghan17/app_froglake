<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Model;

class BookingBusinessHours extends Model
{
    protected $fillable = [
        'day_of_week',
        'is_closed',
        'time_slots',
        'created_by',
        'creator_id'
    ];

    protected $casts = [
        'time_slots' => 'array',
        'is_closed' => 'boolean'
    ];

    public static function getDefaultHours()
    {
        return [
            'monday' => ['is_closed' => false, 'time_slots' => [['open' => '08:30', 'close' => '12:30'], ['open' => '14:00', 'close' => '18:00']]],
            'tuesday' => ['is_closed' => false, 'time_slots' => [['open' => '09:00', 'close' => '13:00'], ['open' => '14:30', 'close' => '17:30']]],
            'wednesday' => ['is_closed' => false, 'time_slots' => [['open' => '08:00', 'close' => '12:00'], ['open' => '13:30', 'close' => '19:00']]],
            'thursday' => ['is_closed' => false, 'time_slots' => [['open' => '09:30', 'close' => '12:30'], ['open' => '15:00', 'close' => '18:30']]],
            'friday' => ['is_closed' => false, 'time_slots' => [['open' => '08:00', 'close' => '11:30'], ['open' => '13:00', 'close' => '16:30']]],
            'saturday' => ['is_closed' => false, 'time_slots' => [['open' => '10:00', 'close' => '14:30'], ['open' => '16:00', 'close' => '20:00']]],
            'sunday' => ['is_closed' => true, 'time_slots' => []]
        ];
    }

    public static function getBusinessHours($createdBy = null)
    {
        $createdBy = $createdBy ?? creatorId();
        $hours = self::where('created_by', $createdBy)->get()->keyBy('day_of_week');
        
        $defaultHours = self::getDefaultHours();
        $result = [];
        
        foreach ($defaultHours as $day => $default) {
            if (isset($hours[$day])) {
                $result[$day] = [
                    'is_closed' => $hours[$day]->is_closed,
                    'time_slots' => $hours[$day]->time_slots ?? []
                ];
            } else {
                $result[$day] = $default;
            }
        }
        
        return $result;
    }
}