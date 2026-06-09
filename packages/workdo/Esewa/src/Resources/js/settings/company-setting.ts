import { CreditCard } from 'lucide-react';

export interface SettingMenuItem {
  order: number;
  title: string;
  href: string;
  icon: any;
  permission: string;
  component: string;
}

export const getEsewaCompanySettings = (t: (key: string) => string): SettingMenuItem[] => [
  {
    order: 1430,
    title: t('Esewa Settings'),
    href: '#esewa-settings',
    icon: CreditCard,
    permission: 'manage-esewa-settings',
    component: 'esewa-settings'
  }
];