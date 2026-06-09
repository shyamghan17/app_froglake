<?php

namespace Workdo\Bookings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Workdo\ProductService\Models\ProductServiceItem;
use Carbon\Carbon;

class BookingAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_number',
        'date',
        'item_id',
        'package_id',
        'staff_id',
        'customer_id',
        'start_time',
        'end_time',
        'payment',
        'payment_status',
        'payment_receipt',
        'online_payment_id',
        'status',
        'created_by',
        'creator_id',
    ];

    public function item()
    {
        return $this->belongsTo(ProductServiceItem::class, 'item_id');
    }

    public function package()
    {
        return $this->belongsTo(BookingPackage::class, 'package_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function customer()
    {
        return $this->belongsTo(BookingCustomer::class, 'customer_id');
    }

    public static function timeSlot($service = null, $date = null, $appointment = null)
    {
        $package = BookingPackage::find($service);

        if ($date && !empty($package)) {

            $selectedDate = Carbon::createFromFormat('Y-m-d', $date);
            $dayName = strtolower($selectedDate->format('l'));

            $businessday = BookingBusinessHours::where('day_of_week', $dayName)
                ->where('created_by', $package->created_by)
                ->first();

            if (!$businessday || $businessday->is_closed || empty($businessday->time_slots)) {
                return [];
            }

            $duration = self::convertToMinutes($package->delivery_time, $package->delivery_period);
            $timeSlots = [];

            foreach ($businessday->time_slots as $timeSlot) {

                $start_time = Carbon::createFromFormat('H:i', $timeSlot['open']);
                $end_time   = Carbon::createFromFormat('H:i', $timeSlot['close']);
                $currentSlot = clone $start_time;

                while ($currentSlot->copy()->addMinutes($duration)->lte($end_time)) {

                    $slotStart = $currentSlot->format('H:i:s');
                    $slotEnd   = $currentSlot->copy()->addMinutes($duration)->format('H:i:s');

                    $slot = [
                        'start' => $slotStart,
                        'end'   => $slotEnd,
                    ];

                    $bookedAppointments = self::isSlotBooked($slot, $package, $date);

                    if (
                        $appointment &&
                        $appointment->start_time == $slotStart &&
                        $appointment->end_time == $slotEnd
                    ) {

                        $timeSlots[] = $slot;
                    } elseif ($bookedAppointments->isEmpty()) {

                        $timeSlots[] = $slot;
                    }

                    $currentSlot->addMinutes($duration);
                }
            }

            return $timeSlots;
        }

        return [];
    }

    public static function convertToMinutes($duration, $period = 'minutes')
    {
        $duration = (int)$duration;

        if ($period === 'hours') {
            return $duration * 60;
        }

        return $duration;
    }


    public static function isSlotBooked($slot, $package, $date)
    {
        return self::where('package_id', $package->id)
            ->where('item_id', $package->item_id)
            ->where('date', $date)
            ->where('start_time', $slot['start'])
            ->where('end_time', $slot['end'])
            ->where('status', '!=', 'cancelled')
            ->get();
    }
}
