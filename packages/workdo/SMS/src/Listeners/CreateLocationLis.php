<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\CMMS\Events\CreateLocation;
use Workdo\SMS\Services\SendSMS;

class CreateLocationLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateLocation $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Location') == 'on') {
            $user = User::find($event->location->created_by);
            $uArr = [
                'company_name' => $user->name ?? '-',
                'location_name' => $event->location->name ?? '-',
            ];
            SendSMS::SendMsgs($uArr, 'New Location', $user->mobile_no ?? null, $user->id);
        }
    }
}
