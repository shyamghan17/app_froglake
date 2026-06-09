<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\CleaningManagement\Events\CreateCleaningInvoice;
use Workdo\CleaningManagement\Models\CleaningInspection;
use Workdo\SMS\Services\SendSMS;

class CreateCleaningInvoiceLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateCleaningInvoice $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Cleaning Invoice') == 'on') {
            $invoice = $event->cleaningInvoice;
            $inspection = CleaningInspection::with('booking')->find($invoice->inspection_id);
            $client = User::where('id', $inspection->booking->user_id)->first();
            $created_by = User::find($client->created_by);

            if ($client->mobile_no) {
                $uArr = [
                    'company_name' => $created_by->name,
                    'user_name' => $client->name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Cleaning Invoice', $client->mobile_no);
            }
        }
    }
}
