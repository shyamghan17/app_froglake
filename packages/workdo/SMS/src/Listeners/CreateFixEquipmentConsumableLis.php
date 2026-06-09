<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\FixEquipment\Events\CreateFixEquipmentConsumable;
use Workdo\SMS\Services\SendSMS;

class CreateFixEquipmentConsumableLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateFixEquipmentConsumable $event)
    {
        $consumable = $event->fixEquipmentConsumable;
        if (Module_is_active('SMS') && company_setting('SMS New Consumables') == 'on') {
            if ($consumable->creator_id != $consumable->created_by) {
                $user = User::find($consumable->created_by) ??  null;
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'company_name' => $user->name ?? '',
                        'name' => $consumable->name ?? $consumable->title ?? '',
                        'assets' => $consumable->asset->asset_name  ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Consumables', $user->mobile_no);
                }
            }
        }
    }
}
