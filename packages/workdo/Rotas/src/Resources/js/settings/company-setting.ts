import { Cog, Calendar } from 'lucide-react';

export interface SettingMenuItem {
  order: number;
  title: string;
  href: string;
  icon: any;
  permission: string;
  component: string;
}

export const getSchoolCompanySettings = (t: (key: string) => string): SettingMenuItem[] => [
  {
    order: 270,
    title: t('Rotas Settings'),
    href: '#rotas-setting',
    icon: Cog,
    permission: 'manage-rotas-settings',
    component: 'rotas-settings'
  },
  {
    order: 280,
    title: t('Rotas Work Schedule'),
    href: '#rotas-work-schedule',
    icon: Calendar,
    permission: 'manage-work-schedule-settings',
    component: 'rotas-work-schedule-settings'
  }
];