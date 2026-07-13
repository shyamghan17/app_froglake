<?php

namespace Workdo\NoticeBoard\Events;

use Workdo\NoticeBoard\Models\Notice;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class CriticalNoticePublished implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Notice $notice,
        public array $targetUserIds
    ) {
        $this->configurePusher();
    }

    private function configurePusher(): void
    {
        $pusherKey     = admin_setting('pusher_app_key');
        $pusherSecret  = admin_setting('pusher_app_secret');
        $pusherAppId   = admin_setting('pusher_app_id');
        $pusherCluster = admin_setting('pusher_app_cluster');

        if ($pusherKey && $pusherSecret && $pusherAppId) {
            Config::set('broadcasting.default', 'pusher');
            Config::set('broadcasting.connections.pusher.key', $pusherKey);
            Config::set('broadcasting.connections.pusher.secret', $pusherSecret);
            Config::set('broadcasting.connections.pusher.app_id', $pusherAppId);
            Config::set('broadcasting.connections.pusher.options.cluster', $pusherCluster);
        }
    }

    public function broadcastOn(): array
    {
        return array_map(
            fn($userId) => new Channel("notice.{$userId}"),
            $this->targetUserIds
        );
    }

    public function broadcastAs(): string
    {
        return 'CriticalNoticePublished';
    }

    public function broadcastWith(): array
    {
        return [
            'id'                     => $this->notice->id,
            'title'                  => $this->notice->title,
            'priority'               => $this->notice->priority,
            'description'            => $this->notice->description,
            'attachments'            => $this->notice->attachments ?? [],
            'require_acknowledgment' => $this->notice->require_acknowledgment,
        ];
    }
}
