<?php

namespace Workdo\SMS\Listeners;

use Workdo\Training\Events\CreateTrainer;
use Workdo\SMS\Services\SendSMS;

class CreateTrainerLis
{
    public function handle(CreateTrainer $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Trainer') == 'on') {
            $trainer = $event->trainer;
            if (isset($trainer->contact)) {
                $uArr = [
                    'user_name' => $trainer->name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Trainer', $trainer->contact, $trainer->created_by);
            }
        }
    }
}
