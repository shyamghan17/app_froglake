import { useState, useEffect, useRef } from 'react';
import { usePage } from '@inertiajs/react';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { getAdminSetting } from '@/utils/helpers';

export interface CriticalNoticeAlert {
    id: number;
    title: string;
    priority: string;
    description: string;
    attachments: string[];
    require_acknowledgment: boolean;
}

export const useCriticalNoticeAlert = () => {
    const pageProps = usePage().props as any;
    const auth = pageProps.auth;
    const [alerts, setAlerts] = useState<CriticalNoticeAlert[]>([]);
    const fetchedRef = useRef(false);

    // Fetch existing unread critical notices on mount — only once, uncomment to enable
    useEffect(() => {
        if (!auth?.user?.id || fetchedRef.current) return;
        fetchedRef.current = true;
        fetch(route('notice-board.critical-alerts'), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        })
            .then((res) => res.json())
            .then((data: CriticalNoticeAlert[]) => setAlerts(data))
            .catch(() => { });
    }, [auth?.user?.id]);

    // Listen via Pusher for new critical notices in real-time
    useEffect(() => {
        if (!auth?.user?.id) return;

        const pusherKey = getAdminSetting('pusher_app_key', pageProps);
        const pusherCluster = getAdminSetting('pusher_app_cluster', pageProps) || 'mt1';

        if (!pusherKey) return;

        try {
            const pusher = new Pusher(pusherKey, { cluster: pusherCluster, forceTLS: true });
            const echo = new Echo({ broadcaster: 'pusher', client: pusher });

            echo.channel(`notice.${auth.user.id}`).listen('.CriticalNoticePublished', (e: CriticalNoticeAlert) => {
                setAlerts((prev) => {
                    const exists = prev.some((a) => a.id === e.id);
                    return exists ? prev : [...prev, e];
                });
            });

            return () => {
                echo.leaveChannel(`notice.${auth.user.id}`);
            };
        } catch {
            // Silently fail — initial fetch still works without Pusher
        }
    }, [auth?.user?.id]);

    const dismiss = (noticeId: number) => {
        fetch(route('notice-board.mark-read', noticeId), {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        }).catch(() => { });

        setAlerts((prev) => prev.filter((a) => a.id !== noticeId));
    };

    const acknowledge = (noticeId: number) => {
        fetch(route('notice-board.acknowledge', noticeId), {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        }).catch(() => { });

        setAlerts((prev) => prev.filter((a) => a.id !== noticeId));
    };

    return { alerts, dismiss, acknowledge };
};
