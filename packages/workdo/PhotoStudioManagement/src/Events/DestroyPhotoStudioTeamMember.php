<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioTeamMember;

class DestroyPhotoStudioTeamMember
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioTeamMember $teamMember
    ) {}
}
