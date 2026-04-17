import { Mail, Inbox, Settings, LayoutGrid } from 'lucide-react';

declare global {
    function route(name: string): string;
}
export const mailboxCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('MailBox'),
        icon: Mail,
        order: 1275,
        href: route('mailbox.inbox'),
        permission: 'manage-mailbox',
    }
];