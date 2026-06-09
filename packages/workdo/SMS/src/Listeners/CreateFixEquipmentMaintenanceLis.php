<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\FixEquipment\Events\CreateFixEquipmentMaintenance;
use Workdo\SMS\Services\SendSMS;

class CreateFixEquipmentMaintenanceLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateFixEquipmentMaintenance $event)
    {
        $maintenance = $event->fixEquipmentMaintenance;
        if (Module_is_active('SMS') && company_setting('SMS New Maintenance') == 'on') {
            if ($maintenance->creator_id != $maintenance->created_by) {
                $user = User::find($maintenance->created_by) ??  null;
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'company_name' => $user->name ?? '',
                        'name' => $maintenance->maintenance_type ?? 'Maintenance',
                        'asset' => $maintenance->asset->asset_name ?? '',
                        'date' =>  $maintenance->maintenance_date->format(company_setting('dateFormat',  $user->id)) ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Maintenance', $user->mobile_no);
                }
            }
        }
    }
}
