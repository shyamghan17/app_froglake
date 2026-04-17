import { MapPin } from 'lucide-react';

export interface SettingMenuItem {
  order: number;
  title: string;
  href: string;
  icon: any;
  permission: string;
  component: string;
}

export const getFindGoogleLeadsCompanySettings = (t: (key: string) => string): SettingMenuItem[] => [
  {
    order: 780,
    title: t('Find Google Leads Settings'),
    href: '#findgoogleleads-settings',
    icon: MapPin,
    permission: 'manage-findgoogleleads-settings',
    component: 'findgoogleleads-settings'
  }
];