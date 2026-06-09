import { MessageSquareDot } from 'lucide-react';

export interface SettingMenuItem {
  order: number;
  title: string;
  href: string;
  icon: any;
  permission: string;
  component: string;
}

export const getBulkSMSCompanySettings = (t: (key: string) => string): SettingMenuItem[] => [
  {
    order: 639,
    title: t('Bulk SMS Settings'),
    href: '#bulksms-settings',
    icon: MessageSquareDot,
    permission: 'manage-bulk-sms',
    component: 'bulksms-settings'
  }
];