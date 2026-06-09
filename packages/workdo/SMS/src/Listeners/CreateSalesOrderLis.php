<?php

namespace Workdo\SMS\Listeners;

use Workdo\Sales\Events\CreateSalesOrder;
use Workdo\SMS\Services\SendSMS;

class CreateSalesOrderLis
{
    public function handle(CreateSalesOrder $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Sales Order') == 'on') {
            $salesOrder = $event->salesOrder;
            if (isset($salesOrder->assignUser->mobile_no)) {
                $uArr = [
                    'sales_order_id' => $salesOrder->quote_number,
                ];
                SendSMS::SendMsgs($uArr, 'New Sales Order', $salesOrder->assignUser->mobile_no ??  null, $salesOrder->created_by ?? null);
            }
        }
    }
}
