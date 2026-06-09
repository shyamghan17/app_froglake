<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\VideoHub\Events\UpdateVideo;

class UpdateVideoHubVideoLis
{
    public function handle(UpdateVideo $event)
    {
        if (Module_is_active('ActivityLog')) {
            $video = $event->videoHubVideo;

            $activity = new AllActivityLog();
            $activity['module'] = 'VideoHub';
            $activity['sub_module'] = 'Video';
            $activity['description'] = __('Video updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $video->created_by;
            $activity->save();
        }
    }
}
