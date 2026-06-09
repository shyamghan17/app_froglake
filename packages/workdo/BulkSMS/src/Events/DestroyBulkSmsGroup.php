<?php

namespace Workdo\BulkSMS\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BulkSMS\Models\BulkSmsGroup;

class DestroyBulkSmsGroup
{
    use Dispatchable;

     public function __construct(
        public BulkSmsGroup  $bulksmsgroup
    ) {}
}