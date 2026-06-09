<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Assets\Events\CreateAssetMaintenance;
use Workdo\SMS\Services\SendSMS;

class CreateAssetMaintenanceLis
{
    public function handle(CreateAssetMaintenance $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Assets Maintenance') == 'on') {
            $maintenance = $event->assetMaintenance;
            if ($maintenance->created_by != $maintenance->creator_id) {
                $user = User::find($maintenance->created_by);
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'asset_name' => $maintenance->asset->name ?? '',
                        'maintenance_type' => $maintenance->maintenance_type ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Assets Maintenance', $user->mobile_no, $maintenance->created_by);
                }
            }
        }
    }
}
