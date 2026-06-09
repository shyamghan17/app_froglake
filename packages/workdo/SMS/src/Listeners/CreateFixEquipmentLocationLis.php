<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\FixEquipment\Events\CreateFixEquipmentLocation;
use Workdo\SMS\Services\SendSMS;

class CreateFixEquipmentLocationLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateFixEquipmentLocation $event)
    {
        $location = $event->fixEquipmentLocation;
        if (Module_is_active('SMS') && company_setting('SMS New Fix Equipment Location') == 'on') {
            if ($location->creator_id != $location->created_by) {
                $user = User::find($location->created_by) ??  null;
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'company_name' => $user->name ?? '',
                        'location_name' => $location->name ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Fix Equipment Location', $user->mobile_no);
                }
            }
        }
    }
}
