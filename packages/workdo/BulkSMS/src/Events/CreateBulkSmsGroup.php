<?php

namespace Workdo\BulkSMS\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\BulkSMS\Models\BulkSmsGroup;

class CreateBulkSmsGroup
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public BulkSmsGroup  $bulksmsgroup
    ) {}
}
