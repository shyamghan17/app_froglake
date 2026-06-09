<?php

namespace Workdo\BulkSMS\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BulkSMS\Models\BulkSmsContact;

class DestroyBulkSmsContact
{
    use Dispatchable;

   public function __construct(
        public BulkSmsContact $bulksmscontact
    ) {}
}