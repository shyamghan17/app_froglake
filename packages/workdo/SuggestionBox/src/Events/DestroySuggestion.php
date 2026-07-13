<?php

namespace Workdo\SuggestionBox\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\SuggestionBox\Models\Suggestion;

class DestroySuggestion
{
    use Dispatchable;

    public function __construct(
        public Suggestion $suggestion
    ) {}
}