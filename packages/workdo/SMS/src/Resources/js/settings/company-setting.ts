import { MessageSquare } from 'lucide-react';

export interface SettingMenuItem {
  order: number;
  title: string;
  href: string;
  icon: any;
  permission: string;
  component: string;
}

export const getSMSCompanySettings = (t: (key: string) => string): SettingMenuItem[] => [
  {
    order: 570,
    title: t('SMS Settings'),
    href: '#sms-settings',
    icon: MessageSquare,
    permission: 'manage-sms-settings',
    component: 'sms-settings'
  }
];