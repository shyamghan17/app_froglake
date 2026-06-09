<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\FixEquipment\Events\CreateFixEquipmentLicense;
use Workdo\SMS\Services\SendSMS;

class CreateFixEquipmentLicenseLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateFixEquipmentLicense $event)
    {
        $license = $event->fixEquipmentLicense;
        if (Module_is_active('SMS') && company_setting('SMS New Licence') == 'on') {

            if ($license->creator_id != $license->created_by) {
                $user = User::find($license->created_by) ??  null;
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'company_name' => $user->name ?? '',
                        'name' => $license->title ?? '',
                        'assets' => $license->asset->asset_name  ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Licence', $user->mobile_no);
                }
            }
        }
    }
}
