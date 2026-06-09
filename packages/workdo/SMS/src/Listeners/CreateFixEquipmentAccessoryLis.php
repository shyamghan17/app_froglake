<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\FixEquipment\Events\CreateFixEquipmentAccessory;
use Workdo\SMS\Services\SendSMS;

class CreateFixEquipmentAccessoryLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateFixEquipmentAccessory $event)
    {
        $accessory = $event->fixEquipmentAccessory;
        if (Module_is_active('SMS') && company_setting('SMS New Accessories') == 'on') {

            $mobile = $accessory->supplier->mobile_no ?? null;
            $supplier_name = $accessory->supplier->name ?? null;
            $company_name = User::find($accessory->created_by);

            if ($mobile) {
                $uArr = [
                    'company_name' => $company_name->name ?? '',
                    'name' => $accessory->title ?? '',
                    'supplier_name' => $supplier_name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Accessories', $mobile);
            }
        }
    }
}
