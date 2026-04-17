<?php

namespace Workdo\MailBox\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\MailBox\Models\MailBoxCredential;

class UpdateMailBoxCredential
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public MailBoxCredential $credential
    ) {}
}