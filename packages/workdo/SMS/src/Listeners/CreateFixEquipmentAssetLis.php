<?php

namespace Workdo\SMS\Listeners;

use Workdo\FixEquipment\Events\CreateFixEquipmentAsset;
use Workdo\SMS\Services\SendSMS;

class CreateFixEquipmentAssetLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateFixEquipmentAsset $event)
    {
        $asset = $event->fixEquipmentAsset;
        if (Module_is_active('SMS') && company_setting('SMS New Asset') == 'on') {

            $mobile = $asset->supplier->mobile_no ?? null;
            $supplier_name = $asset->supplier->name ?? '';
            $location = $asset->location->name ?? '';

            if ($mobile) {
                $uArr = [
                    'name' => $asset->asset_name ?? '',
                    'supplier_name' => $supplier_name,
                    'location' => $location,
                ];
                SendSMS::SendMsgs($uArr, 'New Asset', $mobile, $asset->created_by ?? null);
            }
        }
    }
}
