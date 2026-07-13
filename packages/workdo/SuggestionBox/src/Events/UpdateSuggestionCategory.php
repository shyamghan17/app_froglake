<?php

namespace Workdo\SuggestionBox\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\SuggestionBox\Models\SuggestionCategory;

class UpdateSuggestionCategory
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public SuggestionCategory $suggestioncategory
    ) {}
}