<?php

namespace Workdo\SMS\Listeners;

use Workdo\Spreadsheet\Events\CreateSpreadsheet;
use Workdo\SMS\Services\SendSMS;

class CreateSpreadsheetLis
{
    public function handle(CreateSpreadsheet $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Spreadsheet') == 'on') {
            $user = $event->spreadsheet->creator;
            if (isset($user->mobile_no)) {
                $uArr = [
                    'user_name' => $user->name,
                    'spreadsheet_name' => $event->spreadsheet->name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Spreadsheet', $user->mobile_no, $user->id);
            }
        }
    }
}
