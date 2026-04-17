import { Activity } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const activitylogCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('Activity Log'),
        href: route('activity-logs.index'),
        icon: Activity,
        permission: 'manage-activity-log',
        order: 1325,
    },
];