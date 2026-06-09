<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\FixEquipment\Events\CreateFixEquipmentComponent;
use Workdo\SMS\Services\SendSMS;

class CreateFixEquipmentComponentLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateFixEquipmentComponent $event)
    {
        $component = $event->fixEquipmentComponent;
        if (Module_is_active('SMS') && company_setting('SMS New Fix Equipment Component') == 'on') {
            if ($component->creator_id != $component->created_by) {
                $user = User::find($component->created_by) ??  null;
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'company_name' => company_setting('company_name'),
                        'name' => $component->name ?? $component->title ?? 'Component',
                        'assets' => $component->asset->asset_name  ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Fix Equipment Component', $user->mobile_no);
                }
            }
        }
    }
}
