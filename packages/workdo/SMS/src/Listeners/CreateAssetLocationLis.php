<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Assets\Events\CreateAssetLocation;
use Workdo\SMS\Services\SendSMS;

class CreateAssetLocationLis
{
    public function handle(CreateAssetLocation $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Assets Location') == 'on') {
            $location = $event->assetLocation;
            $user = User::find($location->created_by);
            if (($location->created_by != $location->creator_id) && $user->mobile_no) {
                $uArr = [
                    'location_name' => $location->name ?? '',
                    'company_name' => $user->name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Assets Location', $user->mobile_no, $location->created_by);
            }
        }
    }
}
