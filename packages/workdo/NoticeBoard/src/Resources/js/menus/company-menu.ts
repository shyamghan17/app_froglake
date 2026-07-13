import { ClipboardList } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const noticeboardCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('Notice Board'),
        icon: ClipboardList,
        href: route('notice-board.board'),
        permission: 'manage-notice-board',
        order: 1352,
    },
];
