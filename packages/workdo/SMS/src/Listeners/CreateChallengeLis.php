<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\InnovationCenter\Events\CreateChallenge;
use Workdo\SMS\Services\SendSMS;

class CreateChallengeLis
{
    public function handle(CreateChallenge $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Challenge') == 'on') {
            $challenge = $event->challenge;

            if ($challenge->created_by != $challenge->creator_id) {
                $user = User::find($challenge->created_by);
                if ($user && $user->mobile_no) {
                    switch ($challenge->status) {
                        case 0:
                            $position = 'OnGoing';
                            break;
                        case 1:
                            $position = 'OnHold';
                            break;
                        case 2:
                            $position = 'Finished';
                            break;
                        default:
                            $position = '';
                    }
                    $uArr = [
                        'name' => $challenge->challenge_name ?? '',
                        'position' => $position,
                        'company_name' => $user->name ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Challenge', $user->mobile_no, $challenge->created_by);
                }
            }
        }
    }
}
