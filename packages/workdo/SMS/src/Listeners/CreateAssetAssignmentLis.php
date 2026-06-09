<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Assets\Events\CreateAssetAssignment;
use Workdo\SMS\Services\SendSMS;

class CreateAssetAssignmentLis
{
    public function handle(CreateAssetAssignment $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Assets Assignment') == 'on') {
            $assignment = $event->assetAssignment;
            if ($assignment->user && $assignment->user->mobile_no) {
                $uArr = [
                    'asset_name' => $assignment->asset->name ?? '',
                    'employee_name' => $assignment->user->name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Assets Assignment', $assignment->user->mobile_no, $assignment->created_by);
            }
        }
    }
}
