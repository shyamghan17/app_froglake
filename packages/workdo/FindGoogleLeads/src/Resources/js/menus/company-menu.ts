import { Search } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const findgoogleleadsCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('Find Google Leads'),
        href: route('find-google-leads.index'),
        icon: Search,
        permission: 'manage-find-google-leads',
        order: 1090,
    },
];