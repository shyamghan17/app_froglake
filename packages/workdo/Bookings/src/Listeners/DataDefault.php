<?php

namespace Workdo\Bookings\Listeners;

use App\Events\DefaultData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Workdo\Bookings\Models\BookingSetting;
use Workdo\Bookings\Models\BookingCustomPage;

class DataDefault
{
    public function __construct()
    {
        //
    }

    public function handle(DefaultData $event)
    {
        $company_id = $event->company_id;
        $user_module = $event->user_module ? explode(',', $event->user_module) : [];
        if(!empty($user_module))
        {
            if (in_array("Bookings", $user_module))
            {
                BookingSetting::defaultdata($company_id);

                BookingCustomPage::defaultdata($company_id);
            }
        }
    }
}