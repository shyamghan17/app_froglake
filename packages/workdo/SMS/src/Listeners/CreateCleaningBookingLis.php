<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\CleaningManagement\Events\CreateCleaningBooking;
use Workdo\SMS\Services\SendSMS;

class CreateCleaningBookingLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateCleaningBooking $event)
    {
        $booking = $event->cleaningBooking;
        if (Module_is_active('SMS') && company_setting('SMS New Cleaning Booking') == 'on') {
            $mobile = $booking->phone ??  $booking->user->mobile_no ?? '';
            if ($mobile) {
                $uArr = [
                    'company_name' => User::find($booking->created_by)->name ?? '-',
                    'user_name' => $booking->customer_name ??  $booking->user->name ?? '-',
                ];
                SendSMS::SendMsgs($uArr, 'New Cleaning Booking', $mobile);
            }
        }
    }
}
