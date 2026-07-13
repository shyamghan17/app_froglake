import { BarChart3 } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const smartdashboardanalyticsCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('Smart Dashboard'),
        icon: BarChart3,
        permission: 'manage-smart-dashboard',
        order: 270,
        children: [
            {
                title: t('Executive Overview'),
                href: route('smart-analytics.dashboard'),
                permission: 'manage-smart-dashboard',
            },
            {
                title: t('Financial Analytics'),
                href: route('smart-analytics.financial'),
                permission: 'manage-smart-financial',
            },
            // Changes in hrm module so this page is holding for now, will be added back once hrm module is updated
            // {
            //     title: t('Team Performance'),
            //     href: route('smart-analytics.team'),
            //     permission: 'manage-smart-team',
            // },
            {
                title: t('Sales & Customer'),
                href: route('smart-analytics.sales'),
                permission: 'manage-smart-sales',
            },
            {
                title: t('Operational Analytics'),
                href: route('smart-analytics.operations'),
                permission: 'manage-smart-operations',
            },
        ],
    },
];