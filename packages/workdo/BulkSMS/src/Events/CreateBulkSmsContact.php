<?php

namespace Workdo\BulkSMS\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\BulkSMS\Models\BulkSmsContact;

class CreateBulkSmsContact
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public BulkSmsContact $bulksmscontact
    ) {}
}
