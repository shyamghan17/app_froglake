<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\InnovationCenter\Events\CreateCreativity;
use Workdo\SMS\Services\SendSMS;

class CreateCreativityLis
{
    public function handle(CreateCreativity $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Creativity') == 'on') {
            $creativity = $event->creativity;

            if ($creativity->created_by == $creativity->creator_id) {
                $user = User::find($creativity->created_by);
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'name' => $creativity->creativity_name ?? '',
                        'challenge' => $creativity->challenge->challenge_name ?? '',
                        'company_name' => $user->name ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Creativity', $user->mobile_no, $creativity->created_by);
                }
            }
        }
    }
}
