<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\FixEquipment\Events\CreateFixEquipmentAudit;
use Workdo\FixEquipment\Models\FixEquipmentAsset;
use Workdo\SMS\Services\SendSMS;

class CreateFixEquipmentAuditLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateFixEquipmentAudit $event)
    {
        $audit = $event->fixEquipmentAudit;
        if (Module_is_active('SMS') && company_setting('SMS New Audit') == 'on') {
            if ($audit->creator_id != $audit->created_by) {
                $user = User::find($audit->created_by) ??  null;
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'company_name' => $user->name ?? '',
                        'name' => $audit->title ?? '',
                        'assets' => FixEquipmentAsset::whereIn('id', $audit->asset_ids)->get()->pluck('asset_name')->implode(', ') ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Audit', $user->mobile_no);
                }
            }
        }
    }
}
