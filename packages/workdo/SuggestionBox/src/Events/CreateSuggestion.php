<?php

namespace Workdo\SuggestionBox\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\SuggestionBox\Models\Suggestion;

class CreateSuggestion
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public Suggestion $suggestion
    ) {}
}