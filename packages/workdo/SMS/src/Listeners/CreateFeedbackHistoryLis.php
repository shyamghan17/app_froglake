<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Feedback\Events\CreateHistory;
use Workdo\SMS\Services\SendSMS;

class CreateFeedbackHistoryLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateHistory $event)
    {
        $history = $event->history;
        if (Module_is_active('SMS', $history->created_by) && company_setting('SMS New Rating', $history->created_by) == 'on') {
            $user = User::find($history->created_by) ??  null;
            if ($user && $user->mobile_no) {
                $uArr = [
                    'module_name' => $history->moduleName->module ?? 'Module',
                    'company_name' => $user->name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Rating', $user->mobile_no, $history->created_by);
            }
        }
    }
}
