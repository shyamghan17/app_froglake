<?php

namespace Workdo\SuggestionBox\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\SuggestionBox\Models\SuggestionCategory;

class DestroySuggestionCategory
{
    use Dispatchable;

    public function __construct(
        public SuggestionCategory $suggestioncategory
    ) {}
}