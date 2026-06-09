<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Assets\Events\CreateAsset;
use Workdo\SMS\Services\SendSMS;

class CreateAssetLis
{
    public function handle(CreateAsset $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Assets') == 'on') {
            $asset = $event->asset;
            if ($asset->created_by != $asset->creator_id) {
                $user = User::find($asset->created_by);
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'asset_name' => $asset->name ?? '',
                        'company_name' => $user->name ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Assets', $user->mobile_no, $asset->created_by);
                }
            }
        }
    }
}
