<?php

namespace Workdo\MailBox\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class SendMailBoxEmail
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public array $emailData
    ) {}
}