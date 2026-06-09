import { Receipt } from 'lucide-react';

export const ebillingCompanySetting = (t: (key: string) => string) => [
  {
    order: 1100,
    title: t('eBilling Settings'),
    href: '#ebilling-settings',
    icon: Receipt,
    permission: 'manage-ebilling',
    component: 'ebilling-settings',
  },
];

