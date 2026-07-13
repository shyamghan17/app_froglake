<?php

namespace Workdo\SuggestionBox\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\SuggestionBox\Models\SuggestionStatusHistory;

class DestroySuggestionStatusHistory
{
    use Dispatchable;

    public function __construct(
        public SuggestionStatusHistory $suggestionstatushistory
    ) {}
}