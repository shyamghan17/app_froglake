import { Package, Users, MessageCircleCode } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const bulksmsCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('Bulk SMS'),
        icon: MessageCircleCode,
        permission: 'manage-bulk-sms',
        order: 1255,
        children: [
            {
                title: t('Contacts'),
                href: route('bulk-s-m-s.bulk-sms-contacts.index'),
                permission: 'manage-bulk-sms-contacts',
            },
            {
                title: t('Groups'),
                href: route('bulk-s-m-s.bulk-sms-groups.index'),
                permission: 'manage-bulk-sms-groups',
            },
            {
                title: t('Send Single SMS'),
                href: route('bulk-s-m-s.single-sms.index'),
                permission: 'manage-single-sms',
            },
             {
                title: t('Send Bulk SMS'),
                href: route('bulk-s-m-s.bulksms-group-sms.index'),
                permission: 'manage-bulk-sms-groups-send',
            },
        ],
    },
];