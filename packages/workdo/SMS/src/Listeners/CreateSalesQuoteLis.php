<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Sales\Events\CreateSalesQuote;
use Workdo\SMS\Services\SendSMS;

class CreateSalesQuoteLis
{
    public function handle(CreateSalesQuote $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Quote') == 'on') {
            $quote = $event->quote;
            if (isset($quote->assignUser->mobile_no)) {
                $uArr = [
                    'quotation_id' => $quote->quote_number,
                ];
                SendSMS::SendMsgs($uArr, 'New Quote', $quote->assignUser->mobile_no ?? null, $quote->created_by ?? null);
            }
        }
    }
}
