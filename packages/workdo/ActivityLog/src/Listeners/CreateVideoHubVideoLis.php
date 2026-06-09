<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\VideoHub\Events\CreateVideo;

class CreateVideoHubVideoLis
{
    public function handle(CreateVideo $event)
    {
        if (Module_is_active('ActivityLog')) {
            $video = $event->videoHubVideo;

            $activity = new AllActivityLog();
            $activity['module'] = 'VideoHub';
            $activity['sub_module'] = 'Video';
            $activity['description'] = __('New Video created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $video->created_by;
            $activity->save();
        }
    }
}
