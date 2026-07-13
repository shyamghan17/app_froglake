import {  Inbox } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const suggestionboxCompanyMenu = (t: (key: string) => string) => [
    {
        title     : t('Suggestion Box'),
        icon      : Inbox,
        permission: 'manage-suggestion-box',
        order     : 1353,
        children  : [
            {
                title     : t('Admin Dashboard'),
                href      : route('suggestion-admin.index'),
                permission: 'manage-admin-dashboard',
            },
            {
                title     : t('Suggestions'),
                href      : route('suggestions.index'),
                permission: 'manage-suggestions',
            },
            {
                title     : t('Status Histories'),
                href      : route('status-histories.index'),
                permission: 'manage-suggestion-status-histories',
            },
            {
                title     : t('Categories'),
                href      : route('suggestion-categories.index'),
                permission: 'manage-suggestion-categories',
            },
            
        ],
    },
];